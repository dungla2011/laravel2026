# <meta charset="UTF-8"> <pre>
#https://www.youtube.com/watch?v=nCFgZ5OIiAM
#Git 04: Nhánh trong git, tạo và quản lý nhánh, gộp nhánh với git merge
git init
git touch 0.txt && echo '000' > 0.txt
touch 0.txt && echo '000' > 0.txt
git add .
git commit -m'c0'
touch 1.txt && echo '111' > 1.txt && git add . && git commit -m 'c1'
git status
git log --oneline
touch 2.txt && echo '222' > 2.txt && git add . && git commit -m 'c2'
git branch alpha
git switch alpha
git branch
git log --oneline
touch 3.txt && echo '333' > 3.txt && git add . && git commit -m 'c3'
git log --oneline
echo "them boi alpha" >> 1.txt
git add . && git commit -m 'c4'
git log --oneline

git switch master
git log --oneline
echo "them boi master" >> 0.txt
echo "noi dung 3" > 3.txt
git add . && git commit -m 'c5'
git branch sualoigap
git switch sualoigap

echo "sua loi ABC | sualoigap" >> 1.txt
git add . && git commit -m 'c6'

echo "sua loi 2.txt | sualoigap" >> 2.txt
git add . && git commit -m 'c7'
#gop c7 vao master:
git switch master
git merge -s ours sualoigap -m "Gop master va sualoigap"
git branch -d sualoigap
git merge -s ours alpha -m "Gop master voi alpha"
# nếu muốn bỏ qua sửa đổi:
# git merge --abort
# sửa đổi ... 1.txt, 3.txt
git add . && git commit -m 'c8 - gop nhanh alpha'
git log --oneline --graph

#chuyển về c7, để xem
git checkout ':/c7'
#chuyển về c4, để xem
git checkout ':/c4'

git switch master
echo "sua boi master - 0.txt" >> 0.txt
git add . && git commit -m 'c9'

#sửa 0.txt thêm bởi alpha
git switch alpha
echo "sua boi alpha - 0.txt" >> 0.txt
git add . && git commit -m 'c10'

# gộp alfa, master xem:
git switch master
git merge -s ours alpha -m"gop master voi alpha"
# xung đột, có thể dùng git merge tool , thay vì dùng VSCODE
# chọn nội dung sửa đổi và commit
git add . && git commit -m 'c11 - gop nhanh alpha'


