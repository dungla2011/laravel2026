<meta charset="UTF-8"> <pre>
#30.7.23
#https://www.youtube.com/watch?v=F2pQ3LdtKPg&list=PLwJr0JSP7i8D041yrTcWB_qEdzijIUX-q&index=6
#Git 06: Làm việc với remote repository trên server lưu trữ git, lệnh git push, git pull
#Note: 2 máy cùng dùng remote github, và xử lý xung đột khi add , sửa cùng 1 file
#Nếu cần, phải chạy trên cmd của win:
gh repo create repo123 --public
#gh repo delete repo123 --yes
<style> td { white-space: pre; vertical-align:top} </style>
<pre><table border='1'>
<tr><td style="color: blue">
# Bước 1, khởi tạo content ở máy 1
git init
echo "Noi dung A" >> a.txt
git add . && git commit -m'c1'
echo "Noi dung B" >> b.txt
git add . && git commit -m'c2'
git log
git log --oneline
git checkout -b beta
git log --oneline
echo "Noi dung beta update" >> add-by-beta.txt
git add . && git commit -m'b1'
git log --oneline
git checkout master
echo "Noi dung master" >> add-by-master.txt
git add . && git commit -m'm1'
git log --oneline
git log --oneline beta
git remote -v
git remote add origin git@github.com:dungla2011/repo123.git
git remote -v
git remote add xyz diachi
git remote -v
git remote rm xyz
git remote -v
git remote rm origin
git remote -v
git remote add origin git@github.com:dungla2011/repo123.git
git log --oneline
git push origin master
git log --oneline
git branch
### push lên server
git push -u origin --all
git branch -a
echo "Sua boi master" >> b.txt
git add . && git commit -m'm2'
git log --oneline
# đẩy lên server
git push origin master
git log --oneline
#thử xóa nhánh beta, F5 lại github
git push --delete origin beta
#đẩy lại all nhánh
git push --all

#### Bước 2, sang máy 2
#</td> <td>

#### Bước 2, máy 2
git config --list
git clone git@github.com:dungla2011/repo123.git
git branch
git status
cd repo123/
git status
git branch
git remote -v
git branch -a

### Kéo từ server về
git fetch
git checkout beta
git branch
git switch master
git log --oneline
echo "noi dung c" >> c.txt
git add . && git commit -m"m3"
git log --oneline
git push origin master
# ---Bước 3 - về máy 1
#</td></tr>

#<tr><td  style="color: blue">
# ---Bước 3 - về máy 1
git branch -a
git log --oneline
git fetch
git log --oneline
git status
#->thấy báo lệch 1 phiên bản so với remote

git pull
# thấy file c.txt được thêm vào
git log --oneline
echo "Cap nhat boi PC1" >> c.txt
git add . && git commit -m'm4-pc1'
git log --oneline
# ---Bước 4 - sang máy 2
#</td><td>

# ---Bước 4 - sang máy 2
git log --oneline
echo "Cap nhat boi PC2" >> c.txt
git add . && git commit -m'm4-pc2'
### push lên server
git push origin master
git status
git log --oneline
# ---Bước 5 - về máy 1
#</td></tr>

#<tr><td  style="color: blue">
# ---Bước 5 - về máy 1
# Kéo từ server về
git fetch
git log --oneline
git status
git log --oneline origin/master
git merge origin/master
# xử lý xung đột tại đây: file c.txt có 2 nội dung ...
git status
git add . && git commit -m'm4-merge'
git status
git log --oneline
git push origin master
git status
history
# ---Bước 6 - sang máy 2
#</td><td>
# ---Bước 6 - máy 2
git fetch
git status
git log --oneline origin/master
# Kéo từ server về
git pull origin master
git status
git log --oneline
#</td></tr>
#</table></pre>
