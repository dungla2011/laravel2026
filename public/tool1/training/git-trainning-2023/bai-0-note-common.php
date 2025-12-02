# <meta charset="UTF-8"> <pre>

curl -I -X DELETE -H 'Authorization: token <...>' https://api.github.com/repos/dungla2011/test_git_bai06
#Có thể tạo/xóa github repo, với cách sau, tải gh cli, login 1 lần sau đó tạo/xóa với lênh gh:
gh auth login (trước khi login, tạo một token với các quyền 'repo', 'read:org', 'admin:public_key'.)
gh repo create repo123 --public
gh repo delete repo123 --yes


#Một số lệnh GIT:
#Git 04: Nhánh trong git, tạo và quản lý nhánh, gộp nhánh với git merge
history | cut -c 8-
git merge beta -X ours -m "rebase with beta"
git merge beta -X theirs -m "rebase with beta"

#rebase
git rebase beta -X ours
git rebase beta -X theirs

# xem all tree dạng đồ họa dễ nhìn, bất cứ lúc nào cần debug thêm (Trên windows, cài git, git gui, gitbash...)
gitk --all --date-order

# undo lại cả merge và rebase:
#git reset --merge ORIG_HEAD

#Lệnh này sẽ reset đến cùng Head về gốc mỗi lần gõ thêm
git reset --merge HEAD~1

#vừa Tạo và chuyển sang nhánh beta
git checkout -b beta

# remote:
git remote -v
git push origin beta
git pull origin beta
#để hủy những thay đổi code và lấy trực tiếp code mới nhất từ remote thì:
git reset --hard HEAD
git pull

#git pull = git fetch + git merge


