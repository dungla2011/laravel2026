# <meta charset="UTF-8"> <pre>
#28.7.23
#https://www.youtube.com/watch?v=lZTaQu0lNdI
#Git 05: Gộp nhánh bằng git merge và git rebase
#gộp các nhánh trên local, so sánh merge và rebase
git init
echo "noi dung a - master" >> a.txt
git add .
git commit -m'c1'
git log --oneline
echo "noi dung b - master" >> b.txt
git add . && git commit -m 'c2'
echo "noi dung c - master" >> c.txt
git add . && git commit -m 'c3'
git log --oneline


git branch beta
git checkout beta
#hoac: git checkout -b beta
git log --oneline
git branch
echo "sua boi beta" >> a.txt
git add . && git commit -m 'b1'
git log --oneline
git checkout master
echo "sua boi master" >> a.txt
git add . && git commit -m 'd1'
git log --oneline
echo "sua boi master b.txt" >> b.txt
git add . && git commit -m 'd2'
git log --oneline
git checkout beta
echo "sua boi master c.txt" >> c.txt
git add . && git commit -m 'b2'

git checkout beta
#tạo nhánh b để lưu lại beta
git branch b

git checkout master
#tạo nhánh m để lưu lại master
git branch m

#######################
# Dừng Auto ở đây, để bắt đầu test gộp nhánh

###gộp nhánh beta vào master, dùng Merge:
### Bắt đầu merge:
git checkout master
git merge beta -X ours -m "gop beta vao master"
#git merge beta -X theirs -m "gop beta vao master"
git add . && git commit -m 'M'
git log --oneline

#co the thu lai cac dong tren:
# UNDO:
#git checkout m
#git branch -D master
#git branch master
#git switch master

###gộp nhánh beta vào master, dùng Rebase:
#### Bắt đầu Rebase:
# rebase beta vào master
git checkout m
git branch -D master
git branch master
git switch master
git branch
git rebase beta
git add .
git rebase --continue
git log --oneline
git branch

# test ngược lại, rebase master vào beta
git checkout m
git branch -D master
git branch master
git checkout beta
git rebase master
git add .
git rebase --continue
git status
git log --oneline
