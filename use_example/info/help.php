<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Помощь");
?>

<style>
    .help-wrapper {
        display: flex;
        gap: 40px;

        margin: 0 auto;
        padding: 0px 20px;
    }

    .help-content {
        flex: 1;
        min-width: 0;
    }

    .help-nav {
        width: 300px;
        flex-shrink: 0;
        position: sticky;
        top: 20px;
        align-self: flex-start;
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        border: 1px solid #e9ecef;
    }

    .help-nav h3 {
        margin-top: 0;
        margin-bottom: 16px;
        font-size: 16px;
        color: #333;
        padding-bottom: 10px;
        border-bottom: 2px solid #dee2e6;
    }

    .help-nav ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .help-nav ul li {
        margin-bottom: 8px;
    }

    .help-nav ul li a {
        display: block;
        padding: 8px 12px;
        color: #495057;
        text-decoration: none;
        border-radius: 4px;
        font-size: 14px;
        transition: background 0.2s, color 0.2s;
    }

    .help-nav ul li a:hover {
        background: #e9ecef;
        color: #0056b3;
    }

    .help-nav ul li a.active {
        background: #007bff;
        color: #fff;
    }

    .help-nav ul li ul {
        padding-left: 20px;
        margin-top: 4px;
    }

    .help-nav ul li ul li a {
        font-size: 13px;
        padding: 4px 12px;
        color: #6c757d;
    }

    .help-nav ul li ul li a:hover {
        background: #e9ecef;
        color: #0056b3;
    }

    .info_item {
        margin-bottom: 40px;
        scroll-margin-top: 20px;
    }

    .info_item h2 {
        color: #1a1a1a;
        font-size: 22px;
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 2px solid #e9ecef;
    }

    .info_item h3 {
        font-size: 18px;
        margin-top: 20px;
        margin-bottom: 10px;
        color: #2c3e50;
    }

    .info_item p {
        line-height: 1.6;
        color: #333;
        margin-bottom: 12px;
    }

    .info_item img {
        display: block;
        width: 70%;
        margin: 12px 0;
        border: 1px solid #dee2e6;
        border-radius: 4px;
    }

    /* Кнопка "Наверх" */
    .back-to-top {
        position: fixed;
        bottom: 30px;
        right: 5%;
        width: 50px;
        height: 50px;
        background: #007bff;
        color: #fff;
        border: none;
        border-radius: 50%;
        font-size: 24px;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.4);
        transition: opacity 0.3s, transform 0.3s, background 0.2s;
        opacity: 0;
        visibility: hidden;
        transform: translateY(20px);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .back-to-top.visible {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .back-to-top:hover {
        background: #0056b3;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 123, 255, 0.5);
    }

    .back-to-top:active {
        transform: scale(0.95);
    }

    /* Стрелка внутри кнопки */
    .back-to-top svg {
        width: 24px;
        height: 24px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2.5;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    @media (max-width: 768px) {
        .help-wrapper {
            flex-direction: column;
        }

        .help-nav {
            width: 100%;
            position: static;
            order: -1;
            margin-bottom: 20px;
        }

        .back-to-top {
            bottom: 20px;
            right: 20px;
            width: 44px;
            height: 44px;
            font-size: 20px;
        }
    }
</style>

<div class="help-wrapper">
    <!-- Main Content -->
    <div class="help-content">
        <div class="info_item" id="section1">
            <h2>Как написать сообщение? Возможности формы нового сообщения</h2>
            <p>
                1. Кликаем на "Новое сообщение"
            </p>
            <img src="./help_images/info1.gif" alt="" style="display:block; width:70%;">
            <p>
                2. Вводим содержимое сообщения. При необходимости форматируем текст.
            </p>
            <img src="./help_images/info2.gif" alt="" style="display:block; width:70%;">
            <p>
                3. При необходимости существует возможность прикрепить файлы к сообщению (если прикрепляем картинку, существует возможность вставить её в текст), выбрать дату и время публикации, выбрать получателей, исключить из получателей.
            </p>
            <img src="./help_images/info3.gif" alt="" style="display:block; width:70%;">
        </div>

        <div class="info_item" id="section2">
            <h2>Карточка сообщения</h2>
            <p>
                Если выбрать в редакторе определённый(-ые) отдел(-ы) сотрудников, то сообщения будут отображаться у всех пользователе этого отдела, за исключением пользователей, указанных в поле "Исключить из получателей".
            </p>
            <img src="./help_images/info4.png" alt="" style="display:block; width:70%;">
        </div>

        <div class="info_item" id="section3">
            <h2>Прикрепить ссылку на профиль сотрудника к сообщению</h2>
            <p>
                1. Открыть карточку пользователя
            </p>
            <p>
                2. Скопировать адрес из адресной строки браузера
            </p>
            <img src="./help_images/info5.png" alt="" style="display:block; width:70%;">
            <p>
                3. Добавить ссылку через редактор сообщения. Выбрать из списка вид ссылки "на другой сайт". Ввести текст ссылки (ФИО). Вставить скопированную ссылку в поле адрес (http или https можно оставить по умолчанию, автоматически подстроится). Сохранить
            </p>
            <img src="./help_images/info6.gif" alt="" style="display:block; width:70%;">
        </div>
    </div>

    <!-- Navigation Sidebar -->
    <div class="help-nav">
        <h3>Содержание</h3>
        <ul>
            <li>
                <a href="#section1">Как написать сообщение? Возможности формы нового сообщения</a>
            </li>
            <li>
                <a href="#section2">Карточка сообщения</a>
            </li>
            <li>
                <a href="#section3">Прикрепить ссылку на профиль сотрудника к сообщению</a>
            </li>
        </ul>
    </div>
    <!-- Кнопка "Наверх" -->
<button class="back-to-top" id="backToTop" aria-label="Наверх">
    <svg viewBox="0 0 24 24">
        <polyline points="18 15 12 9 6 15"></polyline>
    </svg>
</button>
</div>



<script>
    // Smooth scroll for navigation links
    document.querySelectorAll('.help-nav a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Кнопка "Наверх"
    (function() {
        const backToTopBtn = document.getElementById('backToTop');
        let isVisible = false;

        // Показываем/скрываем кнопку в зависимости от положения скролла
        function toggleButton() {
            const scrollY = window.scrollY || window.pageYOffset;
            const threshold = 300; // Кнопка появляется после прокрутки на 300px

            if (scrollY > threshold && !isVisible) {
                backToTopBtn.classList.add('visible');
                isVisible = true;
            } else if (scrollY <= threshold && isVisible) {
                backToTopBtn.classList.remove('visible');
                isVisible = false;
            }
        }

        // Обработчик скролла с троттлингом для производительности
        let ticking = false;
        window.addEventListener('scroll', function() {
            if (!ticking) {
                window.requestAnimationFrame(function() {
                    toggleButton();
                    ticking = false;
                });
                ticking = true;
            }
        });

        // Прокрутка наверх при клике
        backToTopBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Проверяем состояние при загрузке страницы
        toggleButton();
    })();
</script>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>