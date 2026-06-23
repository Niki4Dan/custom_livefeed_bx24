<?php
// ========== ПОЛУЧЕНИЕ СПИСКА СМАЙЛИКОВ ИЗ ПАПКИ ==========
function getSmilesFromFolder($folderPath)
{
    $smiles = array();
    $folderPath = rtrim($folderPath, '/');

    if (is_dir($_SERVER['DOCUMENT_ROOT'] . $folderPath)) {
        $files = scandir($_SERVER['DOCUMENT_ROOT'] . $folderPath);
        foreach ($files as $file) {
            // Проверяем расширение .png
            if (preg_match('/\.(png)$/i', $file)) {
                $smiles[] = array(
                    'name' => pathinfo($file, PATHINFO_FILENAME),
                    'file' => $file,
                    'path' => $folderPath . '/' . $file,
                    'fullPath' => $_SERVER['DOCUMENT_ROOT'] . $folderPath . '/' . $file
                );
            }
        }
    }

    usort($smiles, function ($a, $b) {
        return strnatcasecmp($a['file'], $b['file']); // Natural sort
    });

    return $smiles;
}

// Путь к папке со смайликами
$smilesFolder = '/local/components/niki4/smiles';
$arSmilesList = getSmilesFromFolder($smilesFolder);

?>

<script>
    var smilesList = <?= json_encode($arSmilesList) ?>;

    // ========== КНОПКА С МЕНЮ СМАЙЛИКОВ (ФИНАЛЬНАЯ ВЕРСИЯ) ==========
    (function() {
        'use strict';

        // Список смайликов из PHP


        console.log('📦 Загружено смайликов:', smilesList.length);

        // Функция создания меню смайликов
        function createSmilesMenu(editor) {
            // Проверяем, существует ли уже меню для этого редактора
            var existingMenu = document.getElementById('b24-smiles-menu-' + editor.id);
            if (existingMenu) {
                return existingMenu;
            }

            // Создаем контейнер меню
            var menu = document.createElement('div');
            menu.className = 'b24-smiles-menu';
            menu.id = 'b24-smiles-menu-' + editor.id;
            menu.style.cssText = `
            position: fixed !important;
            background: #ffffff !important;
            border: 1px solid #e8e8e8 !important;
            border-radius: 12px !important;
            padding: 12px !important;
            box-shadow: 0 4px 20px rgba(0,0,0,0.25) !important;
            z-index: 999999 !important;
            display: none !important;
            max-width: 720px !important;
            max-height: 300px !important;
            overflow-y: auto !important;
            min-width: 250px !important;
            top: 0 !important;
            left: 0 !important;
            width:720px;
        `;

            // Заголовок
            var header = document.createElement('div');
            header.style.cssText = `
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            margin-bottom: 10px !important;
            padding-bottom: 8px !important;
            border-bottom: 1px solid #f0f2f4 !important;
        `;
            header.innerHTML = `
            <span style="font-size: 14px; font-weight: 600; color: #1a2a3a;">😊 Выберите смайлик</span>
            <button class="b24-smiles-close" style="background: none; border: none; cursor: pointer; font-size: 18px; color: #999; padding: 0 4px; line-height: 1;">×</button>
        `;
            menu.appendChild(header);

            // Контейнер для смайликов
            var grid = document.createElement('div');
            grid.style.cssText = `
            display: grid !important;
            grid-template-columns: repeat(auto-fill, minmax(50px, 1fr)) !important;
            gap: 6px !important;
            padding: 2px !important;
        `;

            // Добавляем смайлики
            if (smilesList && smilesList.length > 0) {
                smilesList.forEach(function(smile) {
                    var item = document.createElement('div');
                    item.className = 'b24-smile-item';
                    item.style.cssText = `
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    padding: 6px !important;
                    border-radius: 8px !important;
                    cursor: pointer !important;
                    transition: all 0.2s ease !important;
                    background: #f8fafc !important;
                    border: 2px solid transparent !important;
                `;

                    var img = document.createElement('img');
                    img.src = smile.path;
                    img.alt = smile.name;
                    img.title = smile.name;
                    img.style.cssText = `
                    max-width: 36px !important;
                    max-height: 36px !important;
                    width: auto !important;
                    height: auto !important;
                    object-fit: contain !important;
                    display: block !important;
                `;

                    img.onerror = function() {
                        this.style.display = 'none';
                        var fallback = document.createElement('span');
                        fallback.textContent = '😊';
                        fallback.style.fontSize = '24px';
                        this.parentNode.appendChild(fallback);
                    };

                    item.appendChild(img);

                    item.addEventListener('mouseenter', function() {
                        this.style.background = '#e8f4fd';
                        this.style.borderColor = '#2fc6f6';
                        this.style.transform = 'scale(1.08)';
                    });

                    item.addEventListener('mouseleave', function() {
                        this.style.background = '#f8fafc';
                        this.style.borderColor = 'transparent';
                        this.style.transform = 'scale(1)';
                    });

                    item.addEventListener('click', function(e) {
                        e.stopPropagation();

                        if (editor && typeof editor.InsertHtml === 'function') {
                            // Вставляем смайлик с классом для идентификации
                            var html = `<img src="${smile.path}" alt="${smile.name}" class="b24-smile-inserted" style="width: 32px; height: 32px; vertical-align: middle; display: inline-block; cursor: pointer;">`;
                            editor.InsertHtml(html);

                            // Скрываем меню
                            var menuEl = document.getElementById('b24-smiles-menu-' + editor.id);
                            if (menuEl) {
                                menuEl.style.display = 'none';
                            }

                            if (typeof showToast === 'function') {
                                showToast('Смайлик "' + smile.name + '" вставлен', 'success', '✅ Готово');
                            }

                            // Принудительно инициализируем ресайз для новых изображений
                            setTimeout(function() {
                                initImageResizeForEditor();
                            }, 300);
                        }
                    });

                    grid.appendChild(item);
                });
            } else {
                var emptyMsg = document.createElement('div');
                emptyMsg.style.cssText = `
                grid-column: 1 / -1;
                text-align: center;
                padding: 20px;
                color: #94a3b8;
                font-size: 14px;
            `;
                emptyMsg.textContent = '😔 Смайлики не найдены';
                grid.appendChild(emptyMsg);
            }

            menu.appendChild(grid);

            // Закрытие по кнопке
            var closeBtn = menu.querySelector('.b24-smiles-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    menu.style.display = 'none';
                });
            }

            // Закрытие при клике вне меню
            document.addEventListener('click', function(e) {
                if (menu.style.display === 'block') {
                    var btn = document.querySelector('.bxhtmled-button-custom-smiles');
                    if (btn && !btn.contains(e.target) && !menu.contains(e.target)) {
                        menu.style.display = 'none';
                    }
                }
            });

            // Добавляем меню в body
            document.body.appendChild(menu);

            return menu;
        }

        // Функция добавления кнопки в редактор
        function addButtonToEditor(editor) {
            if (!editor || !editor.id) return false;

            // Ищем тулбар
            var toolbar = document.querySelector('#bx-html-editor-tlbr-' + editor.id);
            if (!toolbar) {
                return false;
            }

            // Проверяем, есть ли уже кнопка
            if (toolbar.querySelector('.bxhtmled-button-custom-smiles')) {
                return true;
            }

            console.log('➕ Добавляем кнопку смайликов в редактор: ' + editor.id);

            // Создаем меню для этого редактора
            var menu = createSmilesMenu(editor);

            // Добавляем разделитель
            var separator = document.createElement('span');
            separator.className = 'bxhtmled-top-bar-separator';
            toolbar.appendChild(separator);

            // Создаем кнопку
            var btn = document.createElement('span');
            btn.className = 'bxhtmled-top-bar-btn bxhtmled-button-custom-smiles custom-smiles-btn';
            btn.title = 'Смайлики';
            btn.innerHTML = '😊';
            btn.style.cssText = `
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 28px !important;
            height: 28px !important;
            margin: 0 2px !important;
            padding: 0 !important;
            border-radius: 4px !important;
            cursor: pointer !important;
            font-size: 18px !important;
            line-height: 28px !important;
            text-align: center !important;
            background: transparent !important;
            border: none !important;
            transition: all 0.2s ease !important;
            position: relative !important;
        `;

            // Обработчики наведения
            btn.addEventListener('mouseenter', function() {
                this.style.background = '#e8f4fd';
            });
            btn.addEventListener('mouseleave', function() {
                this.style.background = 'transparent';
            });

            // Обработчик клика - открытие/закрытие меню
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                e.preventDefault();

                console.log('🔄 Клик по кнопке смайликов в редакторе: ' + editor.id);

                // Закрываем все меню
                var allMenus = document.querySelectorAll('.b24-smiles-menu');
                allMenus.forEach(function(m) {
                    m.style.display = 'none';
                });

                // Переключаем текущее меню
                if (menu.style.display === 'block') {
                    menu.style.display = 'none';
                    console.log('📋 Меню смайликов закрыто');
                } else {
                    // Позиционируем меню
                    var rect = btn.getBoundingClientRect();
                    var menuWidth = 300; // Примерная ширина меню
                    var left = rect.left - 50;

                    // Проверяем, чтобы меню не выходило за правый край экрана
                    if (left + menuWidth > window.innerWidth) {
                        left = window.innerWidth - menuWidth - 10;
                    }
                    // Проверяем, чтобы меню не выходило за левый край
                    if (left < 10) {
                        left = 10;
                    }

                    menu.style.top = (rect.bottom + 4) + 'px';
                    menu.style.left = left + 'px';
                    menu.style.display = 'block';
                    console.log('📋 Меню смайликов открыто', 'top:', menu.style.top, 'left:', menu.style.left);
                }
            });

            toolbar.appendChild(btn);
            console.log('✅ Кнопка смайликов добавлена в редактор: ' + editor.id);

            return true;
        }

        // Функция добавления кнопки в конкретный редактор по ID
        function addButtonToEditorById(editorId) {
            if (!editorId) return;

            // Ищем редактор
            var editor = null;
            if (window.BXHtmlEditor && window.BXHtmlEditor.editors) {
                for (var i in window.BXHtmlEditor.editors) {
                    if (window.BXHtmlEditor.editors[i].id === editorId) {
                        editor = window.BXHtmlEditor.editors[i];
                        break;
                    }
                }
            }

            if (editor) {
                addButtonToEditor(editor);
            }
        }

        // Запускаем добавление кнопки
        function initSmilesButton() {
            // Сначала пробуем добавить в PREVIEW_TEXT
            addButtonToEditorById('PREVIEW_TEXT');

            // Затем пробуем добавить во все редакторы
            if (window.BXHtmlEditor && window.BXHtmlEditor.editors) {
                for (var i in window.BXHtmlEditor.editors) {
                    var editor = window.BXHtmlEditor.editors[i];
                    if (editor && editor.id && editor.id !== 'PREVIEW_TEXT') {
                        addButtonToEditor(editor);
                    }
                }
            }
        }

        // Запускаем обработку с разными задержками
        BX.ready(function() {
            setTimeout(initSmilesButton, 500);
            setTimeout(initSmilesButton, 1000);
            setTimeout(initSmilesButton, 2000);
            setTimeout(initSmilesButton, 3000);
            setTimeout(initSmilesButton, 5000);

            // Также запускаем при открытии формы (для редакторов, которые создаются динамически)
            var openFormObserver = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                        mutation.addedNodes.forEach(function(node) {
                            if (node.nodeType === Node.ELEMENT_NODE) {
                                // Проверяем, появился ли редактор
                                if (node.querySelector && node.querySelector('.bx-html-editor')) {
                                    setTimeout(initSmilesButton, 500);
                                }
                                if (node.classList && node.classList.contains('bx-html-editor')) {
                                    setTimeout(initSmilesButton, 500);
                                }
                            }
                        });
                    }
                });
            });

            openFormObserver.observe(document.body, {
                childList: true,
                subtree: true
            });
        });

        // Дополнительная проверка: если кнопка не появилась через 5 секунд, пробуем ещё раз
        setTimeout(function() {
            var toolbar = document.querySelector('#bx-html-editor-tlbr-PREVIEW_TEXT');
            if (toolbar && !toolbar.querySelector('.bxhtmled-button-custom-smiles')) {
                console.log('🔄 Повторная попытка добавления кнопки');
                addButtonToEditorById('PREVIEW_TEXT');
            }
        }, 6000);
    })();
</script>