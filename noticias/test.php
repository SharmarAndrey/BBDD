<?php

$cssPath = __DIR__ . "/css/style.css";

if (file_exists($cssPath)) {
    echo "✅ style.css найден!";
} else {
    echo "❌ style.css не найден по пути: $cssPath";
}
