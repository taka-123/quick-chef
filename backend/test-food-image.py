#!/usr/bin/env python3
import os
from PIL import Image, ImageDraw, ImageFont
import random

# 画像サイズ
width, height = 800, 600

# ランダムな背景色
bg_color = (random.randint(200, 255), random.randint(200, 255), random.randint(200, 255))

# 新しい画像を作成
image = Image.new('RGB', (width, height), bg_color)
draw = ImageDraw.Draw(image)

# 食材の図形を描画（簡易的な表現）
# トマト（赤い円）
draw.ellipse([100, 150, 250, 300], fill=(255, 99, 71), outline=(200, 50, 50), width=3)

# レタス（緑の円）
draw.ellipse([300, 200, 500, 400], fill=(144, 238, 144), outline=(34, 139, 34), width=3)

# きゅうり（緑の楕円）
draw.ellipse([550, 100, 700, 350], fill=(60, 179, 113), outline=(0, 100, 0), width=3)

# テキストを追加
try:
    # システムフォントを使用
    font = ImageFont.truetype("/System/Library/Fonts/Helvetica.ttc", 40)
except:
    # フォントが見つからない場合はデフォルトフォントを使用
    font = ImageFont.load_default()

text = "Test Food Image"
text_bbox = draw.textbbox((0, 0), text, font=font)
text_width = text_bbox[2] - text_bbox[0]
text_height = text_bbox[3] - text_bbox[1]
text_x = (width - text_width) // 2
text_y = height - 100

draw.text((text_x, text_y), text, fill=(50, 50, 50), font=font)

# 画像を保存
output_path = "test-food.jpg"
image.save(output_path, "JPEG")
print(f"テスト画像を作成しました: {output_path}")