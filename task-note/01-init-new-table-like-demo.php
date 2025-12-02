<?php

echo "\n --- Giả sử Model tên là Abc";
echo "\n Tạo Model Name là Abc";
echo "\n Tạo Model Meta là Abc_Meta, định nghĩa các URL ";
echo "\n Tạo từ AbcRepositoryInterface";
echo "\n Tạo AbcRepositorySql.php, thay AbcRepositoryInterface và Model Abc vào";
echo "\n Tạo Http Controller API, AbcControllerApi.php, đổi tên, đưa  AbcRepositoryInterface vào";
echo "\n Tạo Http Controller Web, AbcController.php, đưa Model Abc vào";
echo "\n - Vào AppServiceProvider.php:  đăng ký IOC: ";
echo "\n this->app->bind(AbcRepositoryInterface::class , AbcRepositorySql::class); ";
echo "\n Tạo route cho API và Web";

echo "\n Vào DB, tạo table có tên Abcs và các trường (hoặc dùng migrate...) ";

echo "\n Tạo các View Blade nếu cần, ví dụ tree...";
