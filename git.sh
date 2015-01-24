#!/bin/bash
#all archives to master
git init

git add .

git commit -m "adding files"

git remote add origin https://github.com/patadejaguar/S.A.F.E.-Open-Source-Microfinance-Suite.git

git push origin master

#git push origin master --force
