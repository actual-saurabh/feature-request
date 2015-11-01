#! /bin/bash
#
# Script to deploy from Github to WordPress.org Plugin Repository
# A modification of Dean Clatworthy's deploy script as found here: https://github.com/deanc/wordpress-plugin-git-svn
# The difference is that this script lives in the plugin's git repo & doesn't require an existing SVN repo.
# Source: https://github.com/thenbrent/multisite-user-management/blob/master/deploy.sh

#prompt for plugin slug
PLUGINSLUG="feature-request"

# main config, set off of plugin slug
CURRENTDIR=`pwd`
# CURRENTDIR="$CURRENTDIR/$PLUGINSLUG"
CURRENTDIR="$CURRENTDIR"
MAINFILE="$PLUGINSLUG.php" # this should be the name of your main php file in the wordpress plugin

# git config
GITPATH="$CURRENTDIR" # this file should be in the base of your git repository
BUILDPATH="$CURRENTDIR/build/$PLUGINSLUG"

# svn config
SVN_LOCAL_PATH="/LAB/projects/svn/$PLUGINSLUG" # path to a temp SVN repo. No trailing slash required and don't add trunk.
SVNURL="http://plugins.svn.wordpress.org/$PLUGINSLUG/" # Remote SVN repo on WordPress.org, with no trailing slash
SVNUSER="averta" # your svn username

# Let's begin...
echo ".........................................."
echo
echo "Preparing to deploy WordPress plugin"
echo
echo ".........................................."
echo

# Check version in readme.txt is the same as plugin file
# on ubuntu $BUILDPATH/readme.txt seems to have an extra /
NEWVERSION1=`grep "^Stable tag" $BUILDPATH/readme.txt | awk -F' ' '{print $3}'`
if [ "$NEWVERSION1" == "" ]; then  NEWVERSION1=`grep "^Stable tag" $BUILDPATH/readme.md | awk -F' ' '{print $3}'`; fi
echo "readme version: $NEWVERSION1"
NEWVERSION2=`grep "^ \* Version" $BUILDPATH/$MAINFILE | awk -F' ' '{print $3}'`
echo "$MAINFILE version: $NEWVERSION2"

if [ "$NEWVERSION1" != "$NEWVERSION2" ]; then echo "Versions don't match. Exiting...."; exit 1; fi

echo "Versions match in README and PHP file. Let's proceed..."



if [ ! -d "$SVN_LOCAL_PATH" ]; then
  	echo "Creating local copy of SVN repo ..."
  	mkdir $SVN_LOCAL_PATH
	svn co $SVNURL $SVN_LOCAL_PATH
fi


#couldn't get multi line patten above to ignore wp-assets folder
svn propset svn:ignore "deploy.sh"$'\n'"deploy-build.sh"$'\n'"wp-assets"$'\n'"build"$'\n'"README.md"$'\n'"readme.md"$'\n'".git"$'\n'"bower.json"$'\n'"Gruntfile.js"$'\n'".gitignore" "$SVN_LOCAL_PATH/trunk/"

echo "Copying to the trunk of SVN .."

rm -rf `find build -name Thumbs.db`
cp -R "$BUILDPATH/" "$SVN_LOCAL_PATH/trunk/"



echo "Changing directory to SVN and committing to trunk"
cd $SVN_LOCAL_PATH/trunk/

#prompt for plugin slug
echo -e "SVN Commit Message: \c"
read SVNCOMMITMSG

# Add all new files that are not set to be ignored
svn status | grep -v "^.[ \t]*\..*" | grep "^?" | awk '{print $2}' | xargs svn add

# svn commit --username=$SVNUSER -m "$SVNCOMMITMSG"
svn ci --username=$SVNUSER -m "$SVNCOMMITMSG"


echo "Creating new SVN tag & committing it"
cd $SVN_LOCAL_PATH

if [ ! -d "$SVN_LOCAL_PATH/tags/$NEWVERSION1" ]
then
	svn copy trunk/ tags/$NEWVERSION1/
	cd $SVN_LOCAL_PATH/tags/$NEWVERSION1
	svn commit --username=$SVNUSER -m "Tagging version $NEWVERSION1"
fi


echo -e "Update Assets?(Y/N) \c"
read UPDATESVNASSETS

# Add assets
if [[ -d "$GITPATH/wp-assets" && ( "$UPDATESVNASSETS" = "Y" || "$UPDATESVNASSETS" = "y" ) ]]
then

	echo "Changing directory to SVN and committing to assets"
	cd $SVN_LOCAL_PATH/assets
	cp $GITPATH/wp-assets/* .

	svn status | grep -v "^.[ \t]*\..*" | grep "^?" | awk '{print $2}' | xargs svn add
	svn commit --username=$SVNUSER -m "$COMMITMSG"

fi

# echo "Removing temporary directory $SVN_LOCAL_PATH"
# rm -fr $SVN_LOCAL_PATH/

echo "*** FIN ***"
