@echo off
chcp 65001 > nul
title ដំណើរការប្រព័ន្ធគ្រប់គ្រងបុគ្គលិកយោធា (RCAF Personnel Management)

echo ============================================================
echo  🚀 កំពុងបើកដំណើរការប្រព័ន្ធគ្រប់គ្រងបុគ្គលិកយោធា...
echo  💻 អាសយដ្ឋានលើកុំព្យូទ័រ (Computer): http://localhost:8000
echo  📱 អាសយដ្ឋានលើទូរស័ព្ទ (Mobile Wi-Fi): http://192.168.1.15:8000
echo ============================================================

start http://localhost:8000
python server.py

pause
