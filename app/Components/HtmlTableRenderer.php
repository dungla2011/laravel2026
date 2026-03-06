<?php

namespace App\Components;

/**
 * Class HtmlTableRenderer
 * Render any object/array structure as formatted HTML tables
 */
class HtmlTableRenderer
{
    private $title = 'Status Information';
    private $cssStyles = [];

    public function __construct($title = 'Status Information')
    {
        $this->title = $title;
    }

    /**
     * Render object/array to HTML tables
     */
    public function render($data)
    {
        if (is_null($data)) {
            return "<div style='margin: 20px 0; padding: 10px; background-color: #f0f0f0; border: 1px solid #ccc; border-radius: 5px;'>No data available</div>";
        }

        $data = json_decode(json_encode($data), true); // Convert to array recursively

        echo "<div style='margin: 20px 0;'>";
        echo "<h4 style='color: #333; margin-bottom: 15px;'>" . htmlspecialchars($this->title) . "</h4>";

        // Phân tách scalar properties từ complex properties
        $scalarProps = [];
        $complexProps = [];

        foreach ($data as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $complexProps[$key] = $value;
            } else {
                $scalarProps[$key] = $value;
            }
        }

        // Hiển thị bảng tóm tắt scalar properties
        if (!empty($scalarProps)) {
            $this->renderScalarTable($scalarProps);
        }

        // Hiển thị các bảng chi tiết cho complex properties
        foreach ($complexProps as $key => $value) {
            $this->renderComplexTable($key, $value);
        }

        echo "</div>";
    }

    /**
     * Render bảng scalar properties
     */
    private function renderScalarTable($scalarProps)
    {
        echo "<table style='width: 100%; border-collapse: collapse; margin-bottom: 20px; border: 1px solid #ddd;'>";
        $idx = 0;
        foreach ($scalarProps as $key => $value) {
            $bgColor = ($idx % 2) ? '#f5f5f5' : '#ffffff';
            echo "<tr style='background-color: $bgColor;'>";
            echo "<th style='padding: 10px; border: 1px solid #ddd; text-align: left; font-weight: bold; width: 25%;'>" . htmlspecialchars($this->formatKey($key)) . "</th>";
            echo "<td style='padding: 10px; border: 1px solid #ddd;'>" . $this->formatValue($value) . "</td>";
            echo "</tr>";
            $idx++;
        }
        echo "</table>";
    }

    /**
     * Format tên key thành dạng readable
     */
    private function formatKey($key)
    {
        $key = str_replace('_', ' ', $key);
        return ucwords($key);
    }

    /**
     * Format giá trị cho hiển thị
     */
    private function formatValue($value)
    {
        if (is_bool($value)) {
            $color = $value ? '#28a745' : '#dc3545';
            $text = $value ? '✓ Yes' : '✗ No';
            return "<span style='color: $color; font-weight: bold;'>$text</span>";
        }

        if (is_numeric($value) && $value >= 0 && $value <= 100 && strlen((string)$value) <= 3) {
            // Có thể là phần trăm
            return $this->renderProgressBar($value);
        }

        return htmlspecialchars((string)$value);
    }

    /**
     * Render progress bar
     */
    private function renderProgressBar($percent)
    {
        $barColor = $percent == 100 ? '#28a745' : '#ffc107';
        return "<div style='width: 100%; background-color: #eee; border-radius: 5px; overflow: hidden;'>" .
               "<div style='width: " . $percent . "%; background-color: $barColor; height: 20px; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: bold;'>" . $percent . "%</div>" .
               "</div>";
    }

    /**
     * Render bảng cho array/object properties
     */
    private function renderComplexTable($title, $data)
    {
        if (empty($data)) return;

        echo "<h5 style='color: #333; margin-top: 20px; margin-bottom: 10px;'>" . htmlspecialchars($this->formatKey($title)) . "</h5>";

        // Nếu là array of objects (có numeric keys)
        if ($this->isArrayOfObjects($data)) {
            $this->renderTableFromArrayOfObjects($data);
        } else {
            // Nếu là flat object
            $this->renderTableFromObject($data);
        }
    }

    /**
     * Check xem có phải array of objects không
     */
    private function isArrayOfObjects($data)
    {
        if (!is_array($data)) return false;
        if (empty($data)) return false;

        // Nếu có numeric keys từ 0, 1, 2... và value là array/object
        if (isset($data[0]) && (is_array($data[0]) || is_object($data[0]))) {
            return true;
        }

        return false;
    }

    /**
     * Render bảng từ array of objects
     */
    private function renderTableFromArrayOfObjects($items)
    {
        if (empty($items)) return;

        // Collect tất cả keys từ tất cả items
        $allKeys = [];
        foreach ($items as $item) {
            $keys = is_array($item) ? array_keys($item) : array_keys((array)$item);
            $allKeys = array_merge($allKeys, $keys);
        }
        $allKeys = array_unique($allKeys);

        echo "<table style='width: 100%; border-collapse: collapse; border: 1px solid #ddd;'>";

        // Header
        echo "<thead>";
        echo "<tr style='background-color: #007bff; color: white;'>";
        echo "<th style='padding: 10px; border: 1px solid #ddd; text-align: center; font-weight: bold;'>#</th>";
        foreach ($allKeys as $key) {
            echo "<th style='padding: 10px; border: 1px solid #ddd; text-align: left; font-weight: bold;'>" . htmlspecialchars($this->formatKey($key)) . "</th>";
        }
        echo "</tr>";
        echo "</thead>";

        // Body
        echo "<tbody>";
        foreach ($items as $index => $item) {
            $rowBg = ($index % 2) ? '#ffffff' : '#f9f9f9';
            echo "<tr style='background-color: $rowBg;'>";
            echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: center; font-weight: bold;'>" . ($index + 1) . "</td>";

            foreach ($allKeys as $key) {
                $value = $item[$key] ?? $item->{$key} ?? '-';
                
                if (is_array($value) || is_object($value)) {
                    $cellContent = htmlspecialchars(json_encode($value));
                } else {
                    $cellContent = htmlspecialchars((string)$value);
                }
                
                echo "<td style='padding: 10px; border: 1px solid #ddd; font-size: 12px; word-break: break-all;'>$cellContent</td>";
            }

            echo "</tr>";
        }
        echo "</tbody>";

        echo "</table>";
    }

    /**
     * Render bảng từ flat object/array
     */
    private function renderTableFromObject($data)
    {
        echo "<table style='width: 100%; border-collapse: collapse; border: 1px solid #ddd;'>";

        $idx = 0;
        foreach ($data as $key => $value) {
            $bgColor = ($idx % 2) ? '#f5f5f5' : '#ffffff';
            echo "<tr style='background-color: $bgColor;'>";
            echo "<th style='padding: 10px; border: 1px solid #ddd; text-align: left; font-weight: bold; width: 25%;'>" . htmlspecialchars($this->formatKey($key)) . "</th>";

            if (is_array($value) || is_object($value)) {
                $cellContent = htmlspecialchars(json_encode($value));
            } else {
                $cellContent = htmlspecialchars((string)$value);
            }
            
            echo "<td style='padding: 10px; border: 1px solid #ddd; word-break: break-all;'>$cellContent</td>";
            echo "</tr>";
            $idx++;
        }

        echo "</table>";
    }
}
