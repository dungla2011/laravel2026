<?php
// filepath: e:\Projects\laravel2022-01\laravel01\public\tool1\_site\movie\01-crawle-motphim.php
$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'movie1.mytree.vn';

require "/var/www/html/public/index.php";



// require_once 'E:\Projects\laravel2022-01\laravel01\vendor\_ex\simple_html_dom.php';

// Thêm thư viện Simple HTML DOM nếu chưa có
if (!function_exists('str_get_html')) {
    require_once __DIR__ . '/simple_html_dom.php';
    // Hoặc có thể dùng Composer để cài đặt và require autoload
}


/**
 * Hàm lấy nội dung từ URL với User-Agent giả
 */
function getUrlContent($url) {

    try{
        return file_get_content_cache($url);
    }
    catch (Exception $e) {
        echo "\nLỗi: " . $e->getMessage();
    }
    catch (Error $e) {
        echo "\nLỗi: " . $e->getMessage();
    }
    catch (\Throwable $e) {
        echo "\nLỗi: " . $e->getMessage();
    }

    return null;


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Lỗi Curl: ' . curl_error($ch);
        return false;
    }

    curl_close($ch);
    return $result;
}


// URL cần crawl
$url = "https://motphimc.vip/page/1?s&filter%5Bcategories%5D&filter%5Bgenres%5D&filter%5Bregions%5D&filter%5Byears%5D";

function getNpage($url){
    // Lấy nội dung trang
    $html_content = getUrlContent($url);

    if ($html_content) {
        // Khởi tạo Simple HTML DOM
        $html = str_get_html($html_content);

        if ($html) {
            // Tìm phần tử phân trang
            $pagination = $html->find('.pagination', 0);

            $max_page = 1; // Mặc định là trang 1

            if ($pagination) {
                // Tìm tất cả các page-numbers
                $page_numbers = $pagination->find('.page-numbers');

                // Lặp qua các phần tử để tìm số trang lớn nhất
                foreach ($page_numbers as $page) {
                    // Bỏ qua nút "Next" hoặc các phần tử không phải số
                    if ($page->plaintext == '»' || $page->plaintext == '«' || !is_numeric($page->plaintext)) {
                        continue;
                    }

                    $page_num = intval($page->plaintext);
                    if ($page_num > $max_page) {
                        $max_page = $page_num;
                    }
                }
            }

            echo "\nTổng số trang: " . $max_page . "<br>";



            // Bạn có thể lưu số trang lớn nhất vào cơ sở dữ liệu hoặc biến để sử dụng sau

            // Giải phóng bộ nhớ
            $html->clear();
            unset($html);
            return $max_page;
        } else {
            echo "\nKhông thể phân tích HTML";
        }
    } else {
        echo "\nKhông thể lấy nội dung trang";
    }
}
// Tiếp theo, bạn có thể lặp qua từng trang để lấy dữ liệu phim
// Ví dụ:
/*
for ($page = 1; $page <= $max_page; $page++) {
    $page_url = $url . "&page=" . $page;
    // Crawl dữ liệu từ mỗi trang
    crawlMoviesFromPage($page_url);
}
*/

/**
 * Hàm crawl tất cả phim từ một trang và trả về danh sách link phim
 *
 * @param string $url URL trang cần crawl
 * @return array Danh sách link phim và tiêu đề
 */
function crawlMoviesFromPage($url) {
    echo "\n\n\n<h3>Đang quét trang: $url</h3>";

    $html_content = getUrlContent($url);
    if (!$html_content) {
        echo "\n\n<p style='color: red'>Không thể lấy nội dung trang: $url</p>";
        return [];
    }

    return getMovieLinks($html_content);

}

/**
 * Hàm chạy crawl và xử lý nhiều trang
 *
 * @param int $start_page Trang bắt đầu
 * @param int $end_page Trang kết thúc
 * @param int $max_movies Số phim tối đa xử lý (0 = không giới hạn)
 */
function crawlMultiplePages($start_page = 1, $end_page = 3, $max_movies = 0) {
    // Lấy tổng số trang nếu end_page = 0 (tự động)
    if ($end_page <= 0) {
        $base_url = "https://motphimc.vip/?s=&filter%5Bcategories%5D=&filter%5Bgenres%5D=&filter%5Bregions%5D=&filter%5Byears%5D=";
        $total_pages = getNpage($base_url);
        $end_page = $total_pages;
    }

    echo "\n<div style='margin: 20px 0; padding: 15px; background-color: #e9f7ef; border: 1px solid #28a745; border-radius: 5px;'>";
    echo "\n<h2>Bắt đầu quét dữ liệu từ trang $start_page đến trang $end_page</h2>";
    echo "\n</div>";

    $all_movies = [];
    $processed_count = 0;

    // Crawl danh sách phim từ các trang
    for ($page = $start_page; $page <= $end_page; $page++) {
        $page_url = "https://motphimc.vip/page/$page?s&filter%5Bcategories%5D&filter%5Bgenres%5D&filter%5Bregions%5D&filter%5Byears%5D";

        // Lấy danh sách phim từ trang hiện tại
        $movies = crawlMoviesFromPage($page_url);


        // Thêm vào danh sách tổng
        $all_movies = array_merge($all_movies, $movies);

        echo "\n<p>Đã thu thập " . count($movies) . " phim từ trang $page</p>";


        // Kiểm tra giới hạn số phim
        if ($max_movies > 0 && count($all_movies) >= $max_movies) {
            $all_movies = array_slice($all_movies, 0, $max_movies);
            echo "\n<p>Đã đạt giới hạn $max_movies phim, dừng quét.</p>";
            break;
        }

        foreach ($movies AS $one){
//            $one['url'] = "https://motphimc.vip" . $one['url'];
            processMovieFromUrl($one['url']);

//            getch(";....");
        }

    }

    echo "\n<h3>Tổng cộng thu thập được " . count($all_movies) . " phim</h3>";

    return $all_movies;
}

/**
 * Xử lý danh sách phim - ví dụ sử dụng
 */
function processMovieList() {
    // Crawl danh sách phim từ 2 trang đầu tiên
    $movie_list = crawlMultiplePages(1, 2, 10); // Lấy tối đa 10 phim từ 2 trang đầu

    echo "\n<h2>Bắt đầu xử lý các phim đã thu thập...</h2>";

    // Xử lý từng phim một
    foreach ($movie_list as $index => $movie) {
        echo "\n<hr>";
        echo "\n<h3>Xử lý phim " . ($index + 1) . "/" . count($movie_list) . ": {$movie['title']}</h3>";

        // Gọi hàm xử lý từng phim
        $result = processMovieFromUrl($movie['url']);

        if ($result) {
            echo "\n<p style='color: green'>✓ Xử lý thành công</p>";
        } else {
            echo "\n<p style='color: red'>✗ Xử lý thất bại</p>";
        }

        // Tạm dừng giữa các lần xử lý để tránh tải quá mức
        echo "\n<p>Đang tạm dừng 1 giây...</p>";
        sleep(1);
    }

    echo "\n<h2>Hoàn tất xử lý " . count($movie_list) . " phim!</h2>";
}

// Sử dụng:
// processMovieList();

// Hoặc nếu muốn tách riêng các bước:
// $movie_list = crawlMultiplePages(1, 2, 5);
// foreach ($movie_list as $movie) {
//     processMovieFromUrl($movie['url']);
// }

/**
 * Hàm lấy danh sách link và title phim từ trang motphimc.vip
 *
 * @param string $url URL trang cần crawl
 * @return array Mảng chứa các phim với link và title
 */
function getMovieLinks($html_content) {
    // Lấy nội dung trang


    // Phân tích HTML
    $html = str_get_html($html_content);
    if (!$html) {
        echo "\nKhông thể phân tích HTML";
        return [];
    }

    $movies = [];

    // Tìm danh sách phim theo selector
    $list_films = $html->find('.list-films.film-new', 0);

    if ($list_films) {
        // Tìm tất cả các li.item trong danh sách
        $items = $list_films->find('li.item');

        foreach ($items as $item) {
            // Tìm thẻ a trong mỗi item
            $link_element = $item->find('a', 0);

            if ($link_element) {
                $link = $link_element->href;
                $title = $link_element->title ?: $link_element->plaintext;

                // Nếu không tìm thấy title trong thuộc tính, tìm trong thẻ h3
                if (empty(trim($title))) {
                    $title_element = $item->find('h3.title-film', 0);
                    if ($title_element) {
                        $title = trim($title_element->plaintext);
                    }
                }

                // Thêm vào mảng kết quả
                $movies[] = [
                    'url' => $link,
                    'title' => trim($title)
                ];
            }
        }
    }

    // Giải phóng bộ nhớ
    $html->clear();
    unset($html);

    return $movies;
}


// $ret = getMovieLinks($url);

// print_r($ret);



?>

<?php
/**
 * Hàm lấy thông tin thể loại từ trang chi tiết phim
 *
 * @param string $html_content Nội dung HTML của trang chi tiết phim
 * @return array Mảng associative chứa các thể loại với link => tên thể loại
 */
function getMovieGenres($html_content) {
    // Kiểm tra nếu HTML rỗng
    if (empty($html_content)) {
        return [];
    }

    // Phân tích HTML
    $html = str_get_html($html_content);
    if (!$html) {
        return [];
    }

    $genres = [];

    // Tìm phần tử chứa thông tin phim
    $info_div = $html->find('.dinfo', 0);

    if ($info_div) {
        // Tìm tất cả các dt (tiêu đề) trong phần thông tin
        $dt_elements = $info_div->find('dt');

        foreach ($dt_elements as $dt) {
            // Kiểm tra nếu là phần "Thể loại:"
            if (trim($dt->plaintext) == 'Thể loại:') {
                // Lấy phần tử dd ngay sau dt
                $dd = $dt->next_sibling();

                if ($dd) {
                    // Tìm tất cả các thẻ a bên trong dd
                    $genre_links = $dd->find('a');

                    foreach ($genre_links as $link) {
                        $genre_link = $link->href;
                        $genre_name = trim($link->plaintext);

                        // Thêm vào mảng kết quả với key là link, value là tên thể loại
                        $genres[$genre_link] = $genre_name;
                    }
                }

                // Đã tìm thấy và xử lý xong phần thể loại, thoát khỏi vòng lặp
                break;
            }
        }
    }

    // Giải phóng bộ nhớ
    $html->clear();
    unset($html);

    return $genres;
}

/**
 * Hàm lấy tất cả thông tin chi tiết từ trang phim
 *
 * @param string $html_content Nội dung HTML của trang chi tiết phim
 * @return array Mảng thông tin chi tiết phim
 */
function getMovieDetails($html_content) {
    // Kiểm tra nếu HTML rỗng
    if (empty($html_content)) {
        return [];
    }

    // Phân tích HTML
    $html = str_get_html($html_content);
    if (!$html) {
        return [];
    }

    $details = [];

    // Lấy title từ .info > h1 > span.title
    $title_element = $html->find('.info h1 span.title', 0);
    if ($title_element) {
        $details['title'] = trim($title_element->plaintext);
    }

    $img = $html->find('.info .poster img', 0);

    $details['img'] = $img ? $img->src : '';

    // Lấy title_en từ .info > h1 > span.real-name
    $title_en_element = $html->find('.info h2 span.real-name', 0);
    if ($title_en_element) {
        $details['title_en'] = trim(str_replace(['(', ')'], '', $title_en_element->plaintext));
    }

    // Lấy mô tả từ #info-film div.tab và xử lý để loại bỏ thẻ HTML
    $description_element = $html->find('#info-film div.tab', 0);
    if ($description_element) {
        // Lấy HTML và chuyển đổi thành text thuần
        $description_html = $description_element->innertext;

        // Loại bỏ tất cả các thẻ HTML
        $description_text = strip_tags($description_html);

        // Loại bỏ các khoảng trắng thừa và căn chỉnh
        $description_text = preg_replace('/\s+/', ' ', $description_text);
        $description_text = trim($description_text);

        $details['description'] = $description_text;
    }


    // Tìm phần tử chứa thông tin phim
    $info_div = $html->find('.dinfo', 0);

    if ($info_div) {
        // Tìm tất cả các dt (tiêu đề) trong phần thông tin
        $dt_elements = $info_div->find('dt');

        foreach ($dt_elements as $dt) {
            $title = trim($dt->plaintext);
            // Loại bỏ dấu hai chấm nếu có
            $title = rtrim($title, ':');

            // Lấy phần tử dd ngay sau dt
            $dd = $dt->next_sibling();

            if ($dd) {
                // Xử lý tùy theo loại thông tin
                switch ($title) {
                    case 'Thể loại':
                        $genre_links = $dd->find('a');
                        $genres = [];

                        foreach ($genre_links as $link) {
                            $genres[$link->href] = trim($link->plaintext);
                        }

                        $details[$title] = $genres;
                        break;

                    case 'Đạo diễn':
                    case 'Quốc gia':
                    case 'Diễn viên':
                        $links = $dd->find('a');
                        $items = [];

                        foreach ($links as $link) {
                            $items[$link->href] = trim($link->plaintext);
                        }

                        $details[$title] = $items;
                        break;

                    default:
                        // Các thông tin khác lấy text thuần
                        $details[$title] = trim($dd->plaintext);
                        break;
                }
            }
        }
    }

    // Lấy thông tin mô tả phim
    $description = $html->find('.content', 0);
    if ($description) {
        $details['description'] = trim($description->plaintext);
    }

    // Lấy ảnh poster
    $poster = $html->find('.info .poster img', 0);
    if ($poster) {
        $details['poster'] = $poster->src;
    }

    // Giải phóng bộ nhớ
    $html->clear();
    unset($html);

    return $details;
}

/**
 * Hàm lưu thể loại phim vào bảng MediaFolder và trả về mảng ID của tất cả thể loại
 *
 * @param array $genres Mảng thể loại với key là link và value là tên
 * @return array Mảng ID của tất cả thể loại (cả đã tồn tại và mới thêm)
 */
function saveGenresToMediaFolder($genres) {
    if (empty($genres) || !is_array($genres)) {
        echo "\n<p>Không có thể loại nào để lưu!</p>";
        return [];
    }

    $count_new = 0;
    $count_exist = 0;
    $all_ids = []; // Mảng chứa tất cả ID

    foreach ($genres as $link => $name) {
        // Kiểm tra xem thể loại đã tồn tại trong CSDL chưa
        $existing = \App\Models\MediaFolder::where('refer', $link)->first();

        if (!$existing) {
            // Nếu chưa tồn tại, thêm mới
            $folder = \App\Models\MediaFolder::create([
                'name' => $name,
                'refer' => $link,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $count_new++;
            echo "\n<p style='color: green'>✓ Đã thêm thể loại: $name ($link) - ID: {$folder->id}</p>";

            // Thêm ID của folder mới vào mảng kết quả
            $all_ids[] = $folder->id;
        } else {
            // Đã tồn tại, lấy ID và thêm vào mảng kết quả
            $count_exist++;
            echo "\n<p style='color: blue'>→ Thể loại đã tồn tại: $name ($link) - ID: {$existing->id}</p>";

            $all_ids[] = $existing->id;
        }
    }

    echo "\n<div style='margin: 20px 0; padding: 10px; background-color: #f8f9fa; border-left: 5px solid #28a745;'>";
    echo "\n<h4>Kết quả thêm thể loại:</h4>";
    echo "\n<p>- Số thể loại đã thêm mới: <strong>$count_new</strong></p>";
    echo "\n<p>- Số thể loại đã tồn tại: <strong>$count_exist</strong></p>";
    echo "\n<p>- Tổng số thể loại xử lý: <strong>" . ($count_new + $count_exist) . "</strong></p>";
    echo "\n</div>";

    // Trả về mảng ID của tất cả thể loại
    return $all_ids;
}

/**
 * Hàm lưu danh sách diễn viên vào bảng MediaActor và trả về mảng ID
 *
 * @param array $actors Mảng diễn viên với key là link và value là tên
 * @return array Mảng ID của tất cả diễn viên (cả đã tồn tại và mới thêm)
 */
function saveActorsToMediaActor($actors) {
    if (empty($actors) || !is_array($actors)) {
        echo "\n<p>Không có diễn viên nào để lưu!</p>";
        return [];
    }

    $count_new = 0;
    $count_exist = 0;
    $all_ids = []; // Mảng chứa tất cả ID

    foreach ($actors as $link => $name) {
        // Kiểm tra xem diễn viên đã tồn tại trong CSDL chưa
        $existing = \App\Models\MediaActor::where('refer', $link)->first();

        if (!$existing) {
            // Nếu chưa tồn tại, thêm mới
            $actor = \App\Models\MediaActor::create([
                'name' => $name,
                'refer' => $link,
            ]);

            $count_new++;
            echo "\n<p style='color: green'>✓ Đã thêm diễn viên: $name ($link) - ID: {$actor->id}</p>";

            // Thêm ID của diễn viên mới vào mảng kết quả
            $all_ids[] = $actor->id;
        } else {
            // Đã tồn tại, lấy ID và thêm vào mảng kết quả
            $count_exist++;
            echo "\n<p style='color: blue'>→ Diễn viên đã tồn tại: $name ($link) - ID: {$existing->id}</p>";

            $all_ids[] = $existing->id;
        }
    }

    echo "\n<div style='margin: 20px 0; padding: 10px; background-color: #f8f9fa; border-left: 5px solid #28a745;'>";
    echo "\n<h4>Kết quả thêm diễn viên:</h4>";
    echo "\n<p>- Số diễn viên đã thêm mới: <strong>$count_new</strong></p>";
    echo "\n<p>- Số diễn viên đã tồn tại: <strong>$count_exist</strong></p>";
    echo "\n<p>- Tổng số diễn viên xử lý: <strong>" . ($count_new + $count_exist) . "</strong></p>";
    echo "\n</div>";

    // Trả về mảng ID của tất cả diễn viên
    return $all_ids;
}

/**
 * Hàm lưu danh sách đạo diễn vào bảng MediaAuthor và trả về mảng ID
 *
 * @param array $authors Mảng đạo diễn với key là link và value là tên
 * @return array Mảng ID của tất cả đạo diễn (cả đã tồn tại và mới thêm)
 */
function saveAuthorsToMediaAuthor($authors) {
    if (empty($authors) || !is_array($authors)) {
        echo "\n<p>Không có đạo diễn nào để lưu!</p>";
        return [];
    }

    $count_new = 0;
    $count_exist = 0;
    $all_ids = []; // Mảng chứa tất cả ID

    foreach ($authors as $link => $name) {
        // Kiểm tra xem đạo diễn đã tồn tại trong CSDL chưa
        $existing = \App\Models\MediaAuthor::where('refer', $link)->first();

        if (!$existing) {
            // Nếu chưa tồn tại, thêm mới
            $author = \App\Models\MediaAuthor::create([
                'name' => $name,
                'refer' => $link,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $count_new++;
            echo "\n<p style='color: green'>✓ Đã thêm đạo diễn: $name ($link) - ID: {$author->id}</p>";

            // Thêm ID của đạo diễn mới vào mảng kết quả
            $all_ids[] = $author->id;
        } else {
            // Đã tồn tại, lấy ID và thêm vào mảng kết quả
            $count_exist++;
            echo "\n<p style='color: blue'>→ Đạo diễn đã tồn tại: $name ($link) - ID: {$existing->id}</p>";

            $all_ids[] = $existing->id;
        }
    }

    echo "\n<div style='margin: 20px 0; padding: 10px; background-color: #f8f9fa; border-left: 5px solid #28a745;'>";
    echo "\n<h4>Kết quả thêm đạo diễn:</h4>";
    echo "\n<p>- Số đạo diễn đã thêm mới: <strong>$count_new</strong></p>";
    echo "\n<p>- Số đạo diễn đã tồn tại: <strong>$count_exist</strong></p>";
    echo "\n<p>- Tổng số đạo diễn xử lý: <strong>" . ($count_new + $count_exist) . "</strong></p>";
    echo "\n</div>";

    // Trả về mảng ID của tất cả đạo diễn
    return $all_ids;
}

/**
 * Hàm thêm phim vào database, kiểm tra tồn tại qua refer (link phim)
 *
 * @param array $movieDetails Chi tiết phim từ hàm getMovieDetails
 * @param string $movieUrl URL của trang phim
 * @return MediaItem|null Đối tượng MediaItem đã thêm hoặc null nếu có lỗi
 */
function insertMovie($movieDetails, $movieUrl) {
    try {
        // Kiểm tra dữ liệu đầu vào
        if (empty($movieDetails) || empty($movieUrl)) {
            echo "\n<p style='color: red;'>Lỗi: Thông tin phim không đầy đủ!</p>";
            return null;
        }

        // Debug để kiểm tra dữ liệu đầu vào
        echo "\n<pre>Dữ liệu phim: ";
        print_r($movieDetails);
        echo "</pre>";

        // Xử lý các thông tin bổ sung
        $duration = null;
        if (isset($movieDetails['Thời lượng'])) {
            // Trích xuất số phút từ chuỗi, ví dụ: "91 phút" -> 91
//            preg_match('/(\d+)/', $movieDetails['Thời lượng'], $matches);
//            $duration = isset($matches[1]) ? (int)$matches[1] : null;
            $duration = $movieDetails['Thời lượng'];
        }

        $number_part = null;
        if (isset($movieDetails['Số tập'])) {
            // Trích xuất số tập, ví dụ: "1" -> 1
            $number_part = (int)$movieDetails['Số tập'];
        }

        $national = null;
        if (isset($movieDetails['Quốc gia']) && is_array($movieDetails['Quốc gia'])) {
            // Nếu là mảng các quốc gia (link => tên), lấy tên quốc gia đầu tiên
            $national = reset($movieDetails['Quốc gia']);
        } elseif (isset($movieDetails['Quốc gia']) && is_string($movieDetails['Quốc gia'])) {
            $national = $movieDetails['Quốc gia'];
        }

        $year = null;
        if (isset($movieDetails['Năm sản xuất'])) {
            // Lấy năm sản xuất
            $year = (int)$movieDetails['Năm sản xuất'];
        }

        // Tạo mảng dữ liệu cơ bản trước
        $movieData = [
            'name' => $movieDetails['title'] ?? '',
            'name_en' => $movieDetails['title_en'] ?? '',
            'description' => $movieDetails['description'] ?? '',
            'refer' => $movieUrl
        ];

        // Thêm các trường tùy chọn nếu có giá trị
        if ($duration !== null) {
            $movieData['duration'] = $duration;
        }

        if ($number_part !== null) {
            $movieData['number_part'] = $number_part;
        }

        if ($national !== null) {
            $movieData['national'] = $national;
        }

        if ($year !== null) {
            $movieData['year'] = $year;
        }

        // Xử lý ảnh
        $img = $movieDetails['poster'] ?? $movieDetails['img'] ?? '';
        if(!empty($img)) {
            if(!strstr($img, '://') && !strstr($img, 'http')) {
                $img = "https://motphimc.vip" . $img;
            }
            $movieData['thumb'] = $img;

        }

        // Debug để kiểm tra dữ liệu sẽ cập nhật
        echo "\n<pre>Dữ liệu cập nhật: ";
        print_r($movieData);
        echo "</pre>";

        // Kiểm tra phim đã tồn tại qua refer (link phim) chưa
        $existingMovie = \App\Models\MediaItem::where('refer', $movieUrl)->first();

        if ($existingMovie) {
            echo "\n<p style='color: blue;'>Phim đã tồn tại trong database: {$movieDetails['title']} - ID: {$existingMovie->id}</p>";

//            getch("$img ");

            $img = trim($img);
            if($img && !$existingMovie->image_list){
//                getch("xxxxx");
                if($fileCt = file_get_contents($img)){
//                    getch("xxxxx2");
                    $fileSave = "/share/1.png";
                    if(file_put_contents("/share/1.png", $fileCt)){
                    //Tải ảnh này về
                        $ret = \App\Models\FileUpload::uploadFileLocal($fileSave, $movieDetails['title'].'.png',1,0,2, $img);
                        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
                        print_r($ret);
                        echo "</pre>";
//                        getch("...");
                        if(is_numeric($ret)){
//                            $existingMovie->image_list = $ret;
                            $movieData['image_list'] = $ret;
                        }
                    }
                    sleep(1);
                }
            }


            // Cập nhật thông tin phim
            $existingMovie->update($movieData);

            echo "\n<p style='margin-left: 20px;'>→ Đã cập nhật thông tin chi tiết:</p>";
            if (isset($movieData['duration'])) echo "\n<p style='margin-left: 40px;'>Thời lượng: {$movieData['duration']} phút</p>";
            if (isset($movieData['number_part'])) echo "\n<p style='margin-left: 40px;'>Số tập: {$movieData['number_part']}</p>";
            if (isset($movieData['national'])) echo "\n<p style='margin-left: 40px;'>Quốc gia: {$movieData['national']}</p>";
            if (isset($movieData['year'])) echo "\n<p style='margin-left: 40px;'>Năm: {$movieData['year']}</p>";

            // Cập nhật thể loại
            if (!empty($movieDetails['Thể loại'])) {
                $folderIds = saveGenresToMediaFolder($movieDetails['Thể loại']);
                $existingMovie->_folders()->sync($folderIds);
                echo "\n<p style='margin-left: 20px;'>→ Đã cập nhật {$existingMovie->name} với " . count($folderIds) . " thể loại</p>";
            }

            // Cập nhật diễn viên
            if (!empty($movieDetails['Diễn viên'])) {
                $actorIds = saveActorsToMediaActor($movieDetails['Diễn viên']);
                $existingMovie->_actors()->sync($actorIds);
                echo "\n<p style='margin-left: 20px;'>→ Đã cập nhật {$existingMovie->name} với " . count($actorIds) . " diễn viên</p>";
            }

            // Cập nhật đạo diễn
            if (!empty($movieDetails['Đạo diễn'])) {
                $authorIds = saveAuthorsToMediaAuthor($movieDetails['Đạo diễn']);
                $existingMovie->_authors()->sync($authorIds);
                echo "\n<p style='margin-left: 20px;'>→ Đã cập nhật {$existingMovie->name} với " . count($authorIds) . " đạo diễn</p>";
            }

            return $existingMovie;
        } else {
            // Phim chưa tồn tại, thêm mới
            \DB::beginTransaction();

            try {
                $movie = \App\Models\MediaItem::create($movieData);

                echo "\n<p style='color: green;'>✓ Đã thêm phim mới: {$movie->name} - ID: {$movie->id}</p>";

                // Thông tin chi tiết
                if (isset($movieData['duration'])) echo "\n<p style='margin-left: 40px;'>Thời lượng: {$movieData['duration']} phút</p>";
                if (isset($movieData['number_part'])) echo "\n<p style='margin-left: 40px;'>Số tập: {$movieData['number_part']}</p>";
                if (isset($movieData['national'])) echo "\n<p style='margin-left: 40px;'>Quốc gia: {$movieData['national']}</p>";
                if (isset($movieData['year'])) echo "\n<p style='margin-left: 40px;'>Năm: {$movieData['year']}</p>";

                // Thêm thể loại cho phim
                if (!empty($movieDetails['Thể loại'])) {
                    $folderIds = saveGenresToMediaFolder($movieDetails['Thể loại']);
                    foreach ($folderIds as $folderId) {
                        $movie->_folders()->attach($folderId);
                    }
                    echo "\n<p style='margin-left: 20px;'>→ Đã thêm {$movie->name} với " . count($folderIds) . " thể loại</p>";
                }

                // Thêm diễn viên cho phim
                if (!empty($movieDetails['Diễn viên'])) {
                    $actorIds = saveActorsToMediaActor($movieDetails['Diễn viên']);
                    foreach ($actorIds as $actorId) {
                        $movie->_actors()->attach($actorId);
                    }
                    echo "\n<p style='margin-left: 20px;'>→ Đã thêm {$movie->name} với " . count($actorIds) . " diễn viên</p>";
                }

                // Thêm đạo diễn cho phim
                if (!empty($movieDetails['Đạo diễn'])) {
                    $authorIds = saveAuthorsToMediaAuthor($movieDetails['Đạo diễn']);
                    foreach ($authorIds as $authorId) {
                        $movie->_authors()->attach($authorId);
                    }
                    echo "\n<p style='margin-left: 20px;'>→ Đã thêm {$movie->name} với " . count($authorIds) . " đạo diễn</p>";
                }

                \DB::commit();
                return $movie;
            } catch (\Exception $e) {
                \DB::rollBack();
                throw $e; // Ném lại ngoại lệ để catch bên ngoài
            }
        }
    } catch (\Exception $e) {
        if (isset($transaction_started)) {
            \DB::rollBack();
        }
        echo "\n<p style='color: red;'>Lỗi khi thêm phim: " . $e->getMessage() . "</p>";
        echo "\n<p>Tại dòng: " . $e->getLine() . " trong file: " . $e->getFile() . "</p>";
        return null;
    }
}

/**
 * Ví dụ sử dụng hàm insertMovie
 */
function processMovieFromUrl($movieUrl) {
    echo "\n<h3>Đang xử lý phim: {$movieUrl}</h3>";

    // Lấy nội dung HTML của trang phim
    $html_content = getUrlContent($movieUrl);
    if (!$html_content) {
        echo "\n<p style='color: red;'>Không thể lấy nội dung từ URL: {$movieUrl}</p>";
        return null;
    }

    // Lấy chi tiết phim
    $movieDetails = getMovieDetails($html_content);
    if (empty($movieDetails)) {
        echo "\n<p style='color: red;'>Không tìm thấy thông tin phim</p>";
        return null;
    }

    // Hiển thị thông tin phim
    echo "\n<div style='margin: 10px 0; padding: 10px; background-color: #f0f0f0;'>";
    echo "\n<p><strong>Tên phim:</strong> " . ($movieDetails['title'] ?? 'N/A') . "</p>";
    echo "\n<p><strong>Tên tiếng Anh:</strong> " . ($movieDetails['title_en'] ?? 'N/A') . "</p>";

    if (!empty($movieDetails['Thể loại'])) {
        echo "\n<p><strong>Thể loại:</strong> ";
        foreach ($movieDetails['Thể loại'] as $genre) {
            echo "\n{$genre}, ";
        }
        echo "\n</p>";
    }
    echo "\n</div>";

    // Thêm phim vào database
    $movie = insertMovie($movieDetails, $movieUrl);

    return $movie;
}

// // // Test hàm với HTML đầu vào
// Kiểm tra các cột trong bảng
//$columns = \Schema::getColumnListing('media_items');
//print_r($columns);
//return;

//die(get_file_upload_max_size());
// // Lấy tất cả thông tin phim
$test_html = getUrlContent("https://motphimc.vip/phim/ngong-vit-phieu-luu-ky");
// $details = getMovieDetails($test_html);
// echo "\n<h3>Thông tin chi tiết phim:</h3>";
// echo "\n<pre>";
// print_r($details);
// echo "\n</pre>";
//
// return;

// Ví dụ sử dụng
// $testMovieUrl = "https://motphimc.vip/phim/ngong-vit-phieu-luu-ky";
// processMovieFromUrl($testMovieUrl);

//$ct = file_get_contents('https://motphimc.vip/wp-content/uploads/2025/04/hoai-thuy-truc-dinh-17463-thumb.webp');
//
//echo "\n LEN = " . strlen($ct);
//return;

// Cách 1: Sử dụng hàm tiện ích
// processMovieList();

// Cách 2: Tách biệt các bước để kiểm soát chi tiết hơn
$movie_list = crawlMultiplePages(1, 300, 1000000); // Lấy tối đa 20 phim từ 3 trang đầu

// Lưu danh sách phim nếu cần
// file_put_contents('movie_list.json', json_encode($movie_list));

//// Xử lý từng phim
//foreach ($movie_list as $index => $movie) {
//    echo "\n<hr>Xử lý phim {$index}: {$movie['title']}<br>";
//    processMovieFromUrl($movie['url']);
//
////    getch("...");
//
//    // Thêm delay nếu cần
//    sleep(1);
//}

