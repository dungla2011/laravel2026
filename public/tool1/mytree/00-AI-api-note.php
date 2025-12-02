

- Nhiệm vụ: Tạo cây gia phả với N thành viên, M cấp
+ Nhập vào: số N phần tử, và số M là số cấp
+ Cấp dưới luôn có số thành viên nhiều hơn cấp trên
+ Họ các con cháu phải trùng với cha gốc (họ các vợ chồng con cháu thì có thể random khác đi)


- Đây là API add thành viên của cây gia phả:
+ POST vào https://v5.mytree.vn/api/member-tree-mng/add
+ Bearer Token lấy trong .evn: TOKEN_TEST_MYTREE_API

+ Trả về:
{
"code": 1,
"payload": "11218555540340736",
"payloadEx": null,
"message": " Add done 11218555540340736! "
}

Với id ở trong: "payload": "11218555540340736",

- Các trường Post
name:  Tên
parent_id: id của bố mẹ (=0 nghĩa là ở Gốc của Cây), con dâu, rể có cùng parent_id với vợ/chồng. các node có cùng parent_id là anh em, hoac vo chong (nếu là vợ chồng thì sẽ là dâu rể xác định bởi married_with khác rỗng)
married_with: id của vợ chồng
gender: giới tính (1: nam, 2: nữ)
child_of_second_married: id của bố/mẹ lẽ (ví dụ bố có vợ 2, và người này là con vợ 2, thì child_of_second_married của người này = id của vợ 2)

- Ví dụ,
+ Thêm gốc , là Nam, thì cần : name,  parent_id = 0, giả sử id thêm vào là id1
+ Thêm vợ cho Gốc: name,  parent_id = 0, married_with = id1, gender=2 (nữa)
... tương tự cho các nút sau

- hãy tạo mảng Đệm + Tên thôi, còn Họ sẽ Add vào sau (mảng họ random)
và họ các con cháu phải trùng với cha gốc (họ các vợ chồng con cháu thì có thể random khác đi)


