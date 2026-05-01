#!/bin/bash

# Prepare Law Connectors for Server Upload
# This script creates a clean package with ONLY production files

echo "📦 Preparing Law Connectors for upload..."

# Create upload directory
UPLOAD_DIR="law-production"
rm -rf $UPLOAD_DIR
mkdir -p $UPLOAD_DIR

echo "📋 Copying production files..."

# Copy root files
cp app.py $UPLOAD_DIR/
cp chat.php $UPLOAD_DIR/
cp index.php $UPLOAD_DIR/
cp login.php $UPLOAD_DIR/
cp signup.php $UPLOAD_DIR/
cp .env $UPLOAD_DIR/
cp requirements.txt $UPLOAD_DIR/
cp setup.sh $UPLOAD_DIR/
cp lawai.service $UPLOAD_DIR/
cp apache-config.conf $UPLOAD_DIR/

# Copy folders
cp -r admin/ $UPLOAD_DIR/
cp -r student/ $UPLOAD_DIR/
cp -r expert/ $UPLOAD_DIR/
cp -r lib/ $UPLOAD_DIR/
cp -r includes/ $UPLOAD_DIR/
cp -r assets/ $UPLOAD_DIR/
cp -r templates/ $UPLOAD_DIR/
cp -r static/ $UPLOAD_DIR/

echo ""
echo "✅ Production package created: $UPLOAD_DIR/"
echo ""
echo "📊 Contents:"
find $UPLOAD_DIR -type f | wc -l | xargs echo "Total files:"
find $UPLOAD_DIR -type d | wc -l | xargs echo "Total folders:"
echo ""
echo "📤 Ready to upload! Run:"
echo "   scp -r $UPLOAD_DIR/* user@72.61.170.123:/var/www/law/"
echo ""
