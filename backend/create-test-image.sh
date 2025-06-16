#!/bin/bash

# ImageMagickを使用してテスト画像を作成
# macOSではconvertコマンドが使える

# ランダムな背景色でベース画像を作成
convert -size 800x600 xc:"#E8F5E9" base.png

# 食材を表す図形を追加
# トマト（赤い円）
convert base.png -fill "#FF6347" -stroke "#C83232" -strokewidth 3 -draw "circle 175,225 175,150" step1.png

# レタス（緑の円）
convert step1.png -fill "#90EE90" -stroke "#228B22" -strokewidth 3 -draw "circle 400,300 400,200" step2.png

# きゅうり（緑の楕円）
convert step2.png -fill "#3CB371" -stroke "#006400" -strokewidth 3 -draw "ellipse 625,225 75,125 0,360" step3.png

# テキストを追加
convert step3.png -pointsize 40 -fill "#323232" -gravity South -annotate +0+50 "Test Food Image" test-food.jpg

# 中間ファイルを削除
rm -f base.png step1.png step2.png step3.png

echo "テスト画像を作成しました: test-food.jpg"