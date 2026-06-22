<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

// Обработка переключения активности
if ($_GET['active'] == 'Y' && $_GET['id'] && check_bitrix_sessid()) {
	$el = new CIBlockElement;
	$active = ($_GET['set'] == 'Y') ? 'Y' : 'N';
	$el->Update(intval($_GET['id']), array("ACTIVE" => $active));
	LocalRedirect($APPLICATION->GetCurPageParam("", array("active", "set", "sessid")));
}


/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(false);

$colspan = 3;
if ($arResult["CAN_EDIT"] == "Y") $colspan++;
if ($arResult["CAN_DELETE"] == "Y") $colspan++;

?>

<style>
	/* Основные стили */
	.element-list-modern {
		max-width: 1200px;
		margin: 0 auto;
		font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
	}

	/* Сообщения */
	.element-list-modern .note-message {
		background: #d1ecf1;
		border-left: 4px solid #17a2b8;
		color: #0c5460;
		padding: 15px 20px;
		border-radius: 8px;
		margin-bottom: 25px;
		font-size: 14px;
		animation: slideDown 0.3s ease;
	}

	@keyframes slideDown {
		from {
			opacity: 0;
			transform: translateY(-10px);
		}

		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	/* Таблица */
	.element-list-modern .elements-table {
		width: 100%;
		background: #ffffff;
		border-radius: 12px;
		overflow: hidden;
		box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
		border-collapse: separate;
		border-spacing: 0;
	}

	.element-list-modern .elements-table thead tr {
		background-color: #009355;
	}

	.element-list-modern .elements-table th {
		color: #ffffff;
		font-weight: 600;
		font-size: 14px;
		padding: 16px 20px;
		text-align: left;
		letter-spacing: 0.3px;
	}

	.element-list-modern .elements-table tbody tr {
		transition: all 0.2s ease;
		border-bottom: 1px solid #eef2f5;
	}

	.element-list-modern .elements-table tbody tr:hover {
		background: #f8fafc;
		transform: translateX(2px);
	}

	.element-list-modern .elements-table td {
		padding: 16px 20px;
		font-size: 14px;
		color: #2c3e4e;
		vertical-align: middle;
		border-bottom: 1px solid #eef2f5;
	}

	.element-list-modern .elements-table tbody tr:last-child td {
		border-bottom: none;
	}

	/* Название элемента */
	.element-list-modern .element-name {
		font-weight: 600;
		color: #1a2a3a;
		text-decoration: none;
		transition: color 0.2s ease;
		display: inline-block;
	}

	.element-list-modern .element-name:hover {
		color: #0066cc;
		text-decoration: underline;
	}

	/* Статус */
	.element-list-modern .status-badge {
		display: inline-block;
		padding: 4px 12px;
		border-radius: 20px;
		font-size: 12px;
		font-weight: 500;
		letter-spacing: 0.3px;
	}

	.element-list-modern .status-active {
		background: #d4edda;
		color: #155724;
	}

	.element-list-modern .status-inactive {
		background: #f8d7da;
		color: #721c24;
	}

	.element-list-modern .status-waiting {
		background: #fff3cd;
		color: #856404;
	}

	/* Кнопки действий */
	.element-list-modern .action-buttons {
		display: flex;
		gap: 10px;
		flex-wrap: wrap;
	}

	.element-list-modern .btn-edit,
	.element-list-modern .btn-delete {
		display: inline-flex;
		align-items: center;
		gap: 6px;
		padding: 6px 14px;
		border-radius: 6px;
		font-size: 13px;
		font-weight: 500;
		text-decoration: none;
		transition: all 0.2s ease;
		cursor: pointer;
	}

	.element-list-modern .btn-edit {
		background: #e7f3ff;
		color: #0066cc;
		border: 1px solid #b3d9ff;
	}

	.element-list-modern .btn-edit:hover {
		background: #0066cc;
		color: #ffffff;
		transform: translateY(-1px);
		box-shadow: 0 2px 6px rgba(0, 102, 204, 0.3);
	}

	.element-list-modern .btn-delete {
		background: #fee;
		color: #dc3545;
		border: 1px solid #ffccd5;
	}

	.element-list-modern .btn-delete:hover {
		background: #dc3545;
		color: #ffffff;
		transform: translateY(-1px);
		box-shadow: 0 2px 6px rgba(220, 53, 69, 0.3);
	}

	/* Пустое состояние */
	.element-list-modern .empty-state {
		text-align: center;
		padding: 60px 20px;
		background: #f8fafc;
		border-radius: 12px;
	}

	.element-list-modern .empty-icon {
		font-size: 64px;
		margin-bottom: 20px;
		opacity: 0.5;
	}

	.element-list-modern .empty-title {
		font-size: 20px;
		font-weight: 600;
		color: #1a2a3a;
		margin-bottom: 10px;
	}

	.element-list-modern .empty-description {
		color: #7a8a9a;
		font-size: 14px;
	}

	/* Подвал таблицы */
	.element-list-modern .table-footer {
		margin-top: 25px;
		padding: 20px;
		background: #f8fafc;
		border-radius: 12px;
		text-align: center;
	}

	.element-list-modern .add-button {
		display: inline-flex;
		align-items: center;
		gap: 8px;
		padding: 12px 24px;
		background-color: #009355;
		color: #ffffff;
		text-decoration: none;
		border-radius: 8px;
		font-weight: 600;
		font-size: 14px;
		transition: all 0.3s ease;
		box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
	}

	.element-list-modern .add-button:hover {
		transform: translateY(-2px);
		box-shadow: 0 4px 16px rgba(102, 126, 234, 0.4);
	}

	.element-list-modern .cant-add-message {
		display: inline-flex;
		align-items: center;
		gap: 8px;
		padding: 12px 24px;
		background: #f8fafc;
		color: #7a8a9a;
		border-radius: 8px;
		font-size: 14px;
		border: 1px dashed #cbd5e1;
	}

	/* Пагинация */
	.element-list-modern .pagination {
		margin-top: 30px;
		text-align: center;
	}

	.element-list-modern .pagination .modern-pagination {
		display: inline-flex;
		gap: 8px;
		flex-wrap: wrap;
		justify-content: center;
	}

	.element-list-modern .pagination a,
	.element-list-modern .pagination span {
		padding: 8px 14px;
		border-radius: 6px;
		text-decoration: none;
		font-size: 14px;
		transition: all 0.2s ease;
	}

	.element-list-modern .pagination a {
		background: #ffffff;
		color: #0066cc;
		border: 1px solid #e0e4e8;
	}

	.element-list-modern .pagination a:hover {
		background: #0066cc;
		color: #ffffff;
		border-color: #0066cc;
		transform: translateY(-1px);
	}

	.element-list-modern .pagination span {
		background: #0066cc;
		color: #ffffff;
		border: 1px solid #0066cc;
	}

	/* Адаптивность */
	@media (max-width: 768px) {
		.element-list-modern .elements-table {
			display: block;
			overflow-x: auto;
			-webkit-overflow-scrolling: touch;
		}

		.element-list-modern .elements-table th,
		.element-list-modern .elements-table td {
			padding: 12px 15px;
			font-size: 13px;
		}

		.element-list-modern .action-buttons {
			flex-direction: column;
			gap: 8px;
		}

		.element-list-modern .btn-edit,
		.element-list-modern .btn-delete {
			justify-content: center;
		}

		.element-list-modern .add-button {
			width: 100%;
			justify-content: center;
		}

		.element-list-modern .empty-title {
			font-size: 18px;
		}
	}

	/* Анимации */
	@keyframes fadeIn {
		from {
			opacity: 0;
			transform: translateY(20px);
		}

		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	.element-list-modern .elements-table {
		animation: fadeIn 0.4s ease;
	}



	/* Кнопки переключения активности */
	.element-list-modern .btn-toggle-active,
	.element-list-modern .btn-toggle-inactive {
		display: inline-flex;
		align-items: center;
		gap: 6px;
		padding: 6px 14px;
		border-radius: 6px;
		font-size: 13px;
		font-weight: 500;
		text-decoration: none;
		transition: all 0.2s ease;
		cursor: pointer;
		border: none;
	}

	.element-list-modern .btn-toggle-active {
		background: #d4edda;
		color: #155724;
		border: 1px solid #c3e6cb;
	}

	.element-list-modern .btn-toggle-active:hover {
		background: #c3e6cb;
		transform: translateY(-1px);
	}

	.element-list-modern .btn-toggle-inactive {
		background: #f8d7da;
		color: #721c24;
		border: 1px solid #f5c6cb;
	}

	.element-list-modern .btn-toggle-inactive:hover {
		background: #f5c6cb;
		transform: translateY(-1px);
	}
</style>

<div class="element-list-modern">

	<? if ($arResult["MESSAGE"] <> ''): ?>
		<div class="note-message">
			<?= ShowNote($arResult["MESSAGE"]) ?>
		</div>
	<? endif ?>

	<? if ($arResult["NO_USER"] == "N"): ?>

		<table class="elements-table">
			<thead>
				<tr>
					<th><?= GetMessage("IBLOCK_ADD_LIST_TITLE") ?></th>
					<th><?= GetMessage("IBLOCK_ADD_LIST_STATUS") ?></th>
					<th>Активность</th>
					<? if ($arResult["CAN_EDIT"] == "Y"): ?>
						<th><?= GetMessage("IBLOCK_ADD_LIST_EDIT") ?></th>
					<? endif ?>
					<? if ($arResult["CAN_DELETE"] == "Y"): ?>
						<th><?= GetMessage("IBLOCK_ADD_LIST_DELETE") ?></th>
					<? endif ?>
				</tr>
			</thead>
			<tbody>
				<? if (count($arResult["ELEMENTS"]) > 0): ?>
					<? foreach ($arResult["ELEMENTS"] as $arElement): ?>
						<?php
						if ($arElement["IBLOCK_ID"] == 16) {
							$redactor_url = 'redaktor-informatsii/redaktor-flotenk/news_edit.php';
							$detail_url = '/ofitsialnaya-informatsiya/flotenk';
						}
						if ($arElement["IBLOCK_ID"] == 17) {
							$redactor_url = 'redaktor-informatsii/redaktor-flotenk/news_edit.php';
							$detail_url = '/ofitsialnaya-informatsiya/flotenk-inginiring';
						}
						?>
						<tr>
							<td data-label="<?= GetMessage("IBLOCK_ADD_LIST_TITLE") ?>">
								<a <?php if ($arElement["ACTIVE"] == "N") { ?> onclick="event.preventDefault(); alert('Сначала опубликуйте информацию')" <?php } ?> href="<?php if ($arElement["ACTIVE"] == "Y") { ?><?=$detail_url ?>/news_detail.php?ID=<?php echo $arElement["ID"];
																																																															} ?>" class="element-name">
									<?= htmlspecialcharsbx($arElement["NAME"]) ?>
								</a>
							</td>
							<td data-label="<?= GetMessage("IBLOCK_ADD_LIST_STATUS") ?>">
								<?
								// Определяем класс статуса
								$statusClass = 'status-inactive';
								$statusText = '';

								if (is_array($arResult["WF_STATUS"])) {
									$statusText = $arResult["WF_STATUS"][$arElement["WF_STATUS_ID"]];
									if ($arElement["WF_STATUS_ID"] == 1) {
										$statusClass = 'status-active';
									} elseif ($arElement["WF_STATUS_ID"] == 2) {
										$statusClass = 'status-waiting';
									}
								} else {
									$statusText = $arResult["ACTIVE_STATUS"][$arElement["ACTIVE"]];
									$statusClass = ($arElement["ACTIVE"] == 'Y') ? 'status-active' : 'status-inactive';
								}
								?>
								<span class="status-badge <?= $statusClass ?>">
									<?= htmlspecialcharsbx($statusText) ?>
								</span>
							</td>

							<td data-label="Активность">
								<div class="action-buttons">
									<? if ($arElement["ACTIVE"] == "Y"): ?>
										<a href="?active=Y&set=N&id=<?= $arElement["ID"] ?>&<?= bitrix_sessid_get() ?>"
											class="btn-toggle-inactive"
											onclick="return confirm('Перевести новость в черновик? Она станет недоступна для просмотра.')">
											📝 В черновик
										</a>
									<? else: ?>
										<a href="?active=Y&set=Y&id=<?= $arElement["ID"] ?>&<?= bitrix_sessid_get() ?>"
											class="btn-toggle-active"
											onclick="return confirm('Опубликовать новость? Она станет доступна для просмотра.')">
											✅ Опубликовать
										</a>
									<? endif; ?>
								</div>
							</td>
							<? if ($arResult["CAN_EDIT"] == "Y"): ?>
								<td data-label="<?= GetMessage("IBLOCK_ADD_LIST_EDIT") ?>">
									<div class="action-buttons">
										<? if ($arElement["CAN_EDIT"] == "Y"): ?>
											<a href="<?= $arParams["EDIT_URL"] ?>?edit=Y&amp;CODE=<?= $arElement["ID"] ?>" class="btn-edit">
												✏️ <?= GetMessage("IBLOCK_ADD_LIST_EDIT") ?>
											</a>
										<? else: ?>
											<span style="color: #cbd5e1;">—</span>
										<? endif ?>
									</div>
								</td>
							<? endif ?>
							<? if ($arResult["CAN_DELETE"] == "Y"): ?>
								<td data-label="<?= GetMessage("IBLOCK_ADD_LIST_DELETE") ?>">
									<div class="action-buttons">
										<? if ($arElement["CAN_DELETE"] == "Y"): ?>
											<a href="?delete=Y&amp;CODE=<?= $arElement["ID"] ?>&amp;<?= bitrix_sessid_get() ?>"
												class="btn-delete"
												onclick="return confirm('<? echo CUtil::JSEscape(str_replace("#ELEMENT_NAME#", $arElement["NAME"], GetMessage("IBLOCK_ADD_LIST_DELETE_CONFIRM"))) ?>')">
												🗑️ <?= GetMessage("IBLOCK_ADD_LIST_DELETE") ?>
											</a>
										<? else: ?>
											<span style="color: #cbd5e1;">—</span>
										<? endif ?>
									</div>
								</td>
							<? endif ?>
						</tr>
					<? endforeach ?>
				<? else: ?>
					<tr>
						<td colspan="<?= $colspan ?>" style="padding: 0;">
							<div class="empty-state">
								<div class="empty-icon">📭</div>
								<div class="empty-title"><?= GetMessage("IBLOCK_ADD_LIST_EMPTY") ?></div>
								<div class="empty-description">
									<? if (count($arResult["ELEMENTS"]) == 0): ?>
										<a href="<?= $arParams["EDIT_URL"] ?>?edit=Y" class="add-button" style="display: inline-flex; margin-top: 20px;">
											➕ <?= GetMessage("IBLOCK_ADD_LINK_TITLE") ?>
										</a>
									<? endif; ?>
								</div>
							</div>
						</td>
					</tr>
				<? endif ?>
			</tbody>
		</table>

		<div class="table-footer">
			<? if (count($arResult["ELEMENTS"]) > 0): ?>
				<a href="<?= $arParams["EDIT_URL"] ?>?edit=Y" class="add-button">
					➕ <?= GetMessage("IBLOCK_ADD_LINK_TITLE") ?>
				</a>
			<? endif ?>
		</div>

	<? endif; ?>

	<? if ($arResult["NAV_STRING"] <> ''): ?>
		<div class="pagination">
			<div class="modern-pagination">
				<?= $arResult["NAV_STRING"] ?>
			</div>
		</div>
	<? endif ?>

</div>

<? if ($arParams["SET_TITLE"] == "Y"): ?>
	<? $APPLICATION->SetTitle(GetMessage("IBLOCK_ADD_LIST_TITLE")) ?>
<? endif ?>