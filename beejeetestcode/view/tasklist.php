<nav class="navbar navbar-expand-lg navbar-light bg-light">
	<ul class="navbar-nav me-auto mb-2 mb-lg-0">
		<li class="nav-item">
			<? if ($data['is_admin']): ?>
				<a class="nav-link" href="<?= $data['logout_href'] ?>">Выход</a>
			<? else: ?>
				<a class="nav-link" href="<?= $data['auth_href'] ?>">Авторизация</a>
			<? endif; ?>
		</li>
		<li class="nav-item dropdown">
			<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
				Сортировка
			</a>
			<ul class="dropdown-menu">
				<? foreach ($data['sort_types'] as $svalue): ?>
					<li><a class="dropdown-item" href="<?= $svalue['href'] ?>">
							<?= $svalue['name'] ?>
						</a></li>
				<? endforeach; ?>
			</ul>
		</li>
	</ul>
</nav>

<div class="table-responsive">
	<table class="table table-primary">
		<thead>
			<tr>
				<th scope="col">Имя пользователя</th>
				<th scope="col">Email</th>
				<th scope="col">Текст задачи</th>
				<th scope="col">Статус</th>
				<? if ($data['is_admin']): ?>
					<th scope="col">Действия</th>
				<? endif; ?>
			</tr>
		</thead>
		<tbody>
			<? foreach ($data['task_list'] as $key => $value): ?>
				<tr>
					<td>
						<?= $value->userName ?>
					</td>
					<td>
						<?= $value->userEmail ?>
					</td>
					<td>
						<?= $value->text ?>
						<? if ($value->edited): ?>
							<span class="badge bg-danger">changed by admin</span>
						<? endif; ?>
					</td>
					<td>
						<?= ($value->status) ? "Выполнено" : "Не выполнено" ?>
					</td>
					<? if ($data['is_admin']): ?>
						<td>
							<div class="btn-group" role="group">
								<a type="button" class="btn btn-primary"
									href="<?= $data['task_links'][$key]['mark'] ?>">Изменить
									статус</a>
								<a type="button" class="btn btn-primary"
									href="<?= $data['task_links'][$key]['edit'] ?>">Изменить
									текст</a>
							</div>
						</td>
					<? endif; ?>
				</tr>
			<? endforeach; ?>
		</tbody>
	</table>
</div>
<a class="btn btn-success" href="<?= $data['add_href'] ?>" role="button">Добавить</a>
<nav class="mt-2">
	<ul class="pagination">
		<? foreach ($data['pages'] as $value): ?>
			<li class="page-item"><a class="page-link" href="<?= $value['href'] ?>"><?= $value['name'] ?></a></li>
		<? endforeach; ?>
	</ul>
</nav>