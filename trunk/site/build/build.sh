#!/bin/bash

NAME=em0039

# clean up
rm -Rf $NAME-full-package $NAME-full-package.zip $NAME-theme-package $NAME-theme-package.zip $NAME-lightbox $NAME-lightbox.zip

# export code from local repository
svn --ignore-externals export ../ $NAME-full-package/

# copy database files
cat $NAME-full-package/db/1_schema.sql $NAME-full-package/db/2_init_data.sql $NAME-full-package/db/3_BUILD_update_config.sql > sample-database.sql

# remove non-disclosed files
rm -Rf $NAME-full-package/db $NAME-full-package/build $NAME-full-package/app/etc/local.xml-staging $NAME-full-package/app/etc/local.xml-dev $NAME-full-package/app/etc/local.xml.prod

# create $NAME-full-package.zip
mv $NAME-full-package/install-$NAME.php $NAME-full-package/install-$NAME.phps
zip -rq $NAME-full-package.zip $NAME-full-package/ sample-database.sql
echo "Created $NAME-full-package.zip"

### create $NAME-theme-package.zip {{{
mkdir $NAME-theme-package \
	$NAME-theme-package/app \
	$NAME-theme-package/app/design \
	$NAME-theme-package/app/design/adminhtml \
	$NAME-theme-package/app/design/adminhtml/default \
	$NAME-theme-package/app/design/adminhtml/default/default \
	$NAME-theme-package/app/design/adminhtml/default/default/layout \
	$NAME-theme-package/app/design/adminhtml/default/default/template \
	$NAME-theme-package/app/design/frontend \
	$NAME-theme-package/app/design/frontend/base \
	$NAME-theme-package/app/design/frontend/base/default \
	$NAME-theme-package/app/design/frontend/base/default/layout \
	$NAME-theme-package/app/design/frontend/base/default/template \
	$NAME-theme-package/skin \
	$NAME-theme-package/skin/adminhtml \
	$NAME-theme-package/skin/adminhtml/default \
	$NAME-theme-package/skin/adminhtml/default/default \
	$NAME-theme-package/skin/adminhtml/default/default/em \
	$NAME-theme-package/skin/frontend \
	$NAME-theme-package/skin/frontend/base \
	$NAME-theme-package/skin/frontend/base/default \
	$NAME-theme-package/skin/frontend/base/default/css \
	$NAME-theme-package/skin/frontend/base/default/images \
	$NAME-theme-package/skin/frontend/base/default/js \
	$NAME-theme-package/skin/frontend/base/default/media \
	$NAME-theme-package/app/code \
	$NAME-theme-package/app/code/community \
	$NAME-theme-package/app/code/community/EM \
	$NAME-theme-package/app/code/community/Morningtime \
	$NAME-theme-package/app/code/local \
	$NAME-theme-package/app/code/local/EM \
	$NAME-theme-package/app/etc \
	$NAME-theme-package/app/etc/modules \
	$NAME-theme-package/js \
	$NAME-theme-package/js/em \
	$NAME-theme-package/js/media \
	$NAME-theme-package/media

# theme
mv $NAME-full-package/app/design/frontend/$NAME $NAME-theme-package/app/design/frontend
mv $NAME-full-package/skin/frontend/$NAME $NAME-theme-package/skin/frontend

# EM0039 Settings
mv $NAME-full-package/app/code/community/EM/Em0022settings $NAME-theme-package/app/code/community/EM/
mv $NAME-full-package/app/etc/modules/EM_Em0022settings.xml $NAME-theme-package/app/etc/modules/

# MegaMenu extension
mv $NAME-full-package/app/code/community/EM/Megamenu $NAME-theme-package/app/code/community/EM/
mv $NAME-full-package/app/etc/modules/EM_Megamenu.xml $NAME-theme-package/app/etc/modules/
mv $NAME-full-package/app/design/frontend/base/default/layout/em_megamenu.xml $NAME-theme-package/app/design/frontend/base/default/layout/
mv $NAME-full-package/app/design/frontend/base/default/template/em_megamenu $NAME-theme-package/app/design/frontend/base/default/template/
mv $NAME-full-package/skin/frontend/base/default/css/em_megamenu.css $NAME-theme-package/skin/frontend/base/default/css/

# CloudZoom /
mv $NAME-full-package/app/code/local/EM/Cloudzoom $NAME-theme-package/app/code/local/EM/
mv $NAME-full-package/app/etc/modules/EM_Cloudzoom.xml $NAME-theme-package/app/etc/modules/
mv $NAME-full-package/app/design/frontend/base/default/layout/em_cloudzoom.xml $NAME-theme-package/app/design/frontend/base/default/layout/
mv $NAME-full-package/app/design/frontend/base/default/template/em_cloudzoom $NAME-theme-package/app/design/frontend/base/default/template/
mv $NAME-full-package/skin/frontend/base/default/css/em_cloudzoom $NAME-theme-package/skin/frontend/base/default/css/
mv $NAME-full-package/skin/frontend/base/default/js/em_cloudzoom $NAME-theme-package/skin/frontend/base/default/js/

# ajaxcart
mv $NAME-full-package/app/code/community/EM/Ajaxcart $NAME-theme-package/app/code/community/EM/
mv $NAME-full-package/app/etc/modules/EM_Ajaxcart.xml $NAME-theme-package/app/etc/modules/
mv $NAME-full-package/app/design/frontend/base/default/layout/em_ajaxcart.xml $NAME-theme-package/app/design/frontend/base/default/layout/
mv $NAME-full-package/app/design/frontend/base/default/template/em_ajaxcart $NAME-theme-package/app/design/frontend/base/default/template/
mv $NAME-full-package/skin/frontend/base/default/css/em_ajaxcart $NAME-theme-package/skin/frontend/base/default/css/
mv $NAME-full-package/skin/frontend/base/default/js/em_ajaxcart $NAME-theme-package/skin/frontend/base/default/js/
mv $NAME-full-package/skin/frontend/base/default/images/em_ajaxcart $NAME-theme-package/skin/frontend/base/default/images/

# Flexible Widget extension
mv $NAME-full-package/app/code/community/EM/Flexiblewidget $NAME-theme-package/app/code/community/EM/
mv $NAME-full-package/app/design/frontend/base/default/template/flexiblewidget $NAME-theme-package/app/design/frontend/base/default/template/
mv $NAME-full-package/app/etc/modules/EM_Flexiblewidget.xml $NAME-theme-package/app/etc/modules/

# Morningtime Latest Review extension
mv $NAME-full-package/app/code/community/Morningtime/LatestReviews $NAME-theme-package/app/code/community/Morningtime/
mv $NAME-full-package/app/etc/modules/Morningtime_LatestReviews.xml $NAME-theme-package/app/etc/modules/

# Slideshow Widget extension
mv $NAME-full-package/app/code/local/EM/SlideshowWidget $NAME-theme-package/app/code/local/EM/
mv $NAME-full-package/app/etc/modules/EM_SlideshowWidget.xml $NAME-theme-package/app/etc/modules/
mv $NAME-full-package/app/design/frontend/base/default/layout/slideshowwidget.xml $NAME-theme-package/app/design/frontend/base/default/layout/
mv $NAME-full-package/app/design/frontend/base/default/template/slideshowwidget $NAME-theme-package/app/design/frontend/base/default/template/
mv $NAME-full-package/app/design/adminhtml/default/default/template/slideshowwidget $NAME-theme-package/app/design/adminhtml/default/default/template/
mv $NAME-full-package/skin/frontend/base/default/slideshowwidget $NAME-theme-package/skin/frontend/base/default/
mv $NAME-full-package/media/slideshow $NAME-theme-package/media/

# install script
mv $NAME-full-package/install.lib.php $NAME-theme-package/
mv $NAME-full-package/install-$NAME.phps $NAME-theme-package/

zip -rq $NAME-theme-package.zip $NAME-theme-package/
echo "Created $NAME-theme-package.zip"
### }}}

# create $NAME-lightbox.zip
mkdir $NAME-lightbox \
	$NAME-lightbox/app \
	$NAME-lightbox/app/design \
	$NAME-lightbox/app/design/frontend \
	$NAME-lightbox/app/design/frontend/$NAME \
	$NAME-lightbox/app/design/frontend/$NAME/default \
	$NAME-lightbox/app/design/frontend/$NAME/default/template \
	$NAME-lightbox/app/design/frontend/$NAME/default/template/catalog \
	$NAME-lightbox/app/design/frontend/$NAME/default/template/catalog/product \
	$NAME-lightbox/app/design/frontend/$NAME/default/template/catalog/product/view \
	$NAME-lightbox/app/design/frontend/$NAME/default/template/page \
	$NAME-lightbox/app/design/frontend/$NAME/default/template/page/html \
	$NAME-lightbox/skin \
	$NAME-lightbox/skin/frontend \
	$NAME-lightbox/skin/frontend/$NAME \
	$NAME-lightbox/skin/frontend/$NAME/default \
	$NAME-lightbox/skin/frontend/$NAME/default/css \
	$NAME-lightbox/skin/frontend/$NAME/default/js \
	$NAME-lightbox/skin/frontend/$NAME/default/images
cp $NAME-theme-package/app/design/frontend/$NAME/default/template/catalog/product/view/media_lightbox.phtml $NAME-lightbox/app/design/frontend/$NAME/default/template/catalog/product/view/
cp $NAME-theme-package/app/design/frontend/$NAME/default/template/page/html/head.phtml $NAME-lightbox/app/design/frontend/$NAME/default/template/page/html/
cp $NAME-theme-package/skin/frontend/$NAME/default/css/lightbox.css $NAME-lightbox/skin/frontend/$NAME/default/css/
cp $NAME-theme-package/skin/frontend/$NAME/default/js/lightbox.js $NAME-lightbox/skin/frontend/$NAME/default/js/
cp $NAME-theme-package/skin/frontend/$NAME/default/images/bullet.gif $NAME-lightbox/skin/frontend/$NAME/default/images/
cp $NAME-theme-package/skin/frontend/$NAME/default/images/close.gif $NAME-lightbox/skin/frontend/$NAME/default/images/
cp $NAME-theme-package/skin/frontend/$NAME/default/images/closelabel.gif $NAME-lightbox/skin/frontend/$NAME/default/images/
cp $NAME-theme-package/skin/frontend/$NAME/default/images/loading.gif $NAME-lightbox/skin/frontend/$NAME/default/images/
cp $NAME-theme-package/skin/frontend/$NAME/default/images/nextlabel.gif $NAME-lightbox/skin/frontend/$NAME/default/images/
cp $NAME-theme-package/skin/frontend/$NAME/default/images/prevlabel.gif $NAME-lightbox/skin/frontend/$NAME/default/images/
zip -rq $NAME-lightbox.zip $NAME-lightbox/
echo "Created $NAME-lightbox.zip"

#clean up
rm -Rf $NAME-theme-package
rm -Rf $NAME-full-package
rm -Rf $NAME-lightbox
rm -Rf sample-database.sql