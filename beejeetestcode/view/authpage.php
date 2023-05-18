<h3>Вход</h3>
<form action="/auth" method="post">
    <input type="hidden" name="backurl" value="<?= $data['backurl'] ?>">
    <div class="mb-3">
        <label class="form-label">Логин</label>
        <? if (isset($data['errors']['login'])): ?>
            <input type="text" class="form-control is-invalid" name="login" value="<?= $data['values']['login'] ?>">
            <div class="invalid-feedback">
                <?= $data['errors']['login'] ?>
            </div>
        <? else: ?>
            <input type="text" class="form-control" name="login" value="<?= $data['values']['login'] ?>">
        <? endif; ?>
    </div>
    <div class="mb-3">
        <label class="form-label">Пароль</label>
        <? if (isset($data['errors']['password'])): ?>
            <input type="password" class="form-control is-invalid" name="password"
                value="<?= $data['values']['password'] ?>">
            <div class="invalid-feedback">
                <?= $data['errors']['password'] ?>
            </div>
        <? else: ?>
            <input type="password" class="form-control" name="password" value="<?= $data['values']['password'] ?>">
        <? endif; ?>
    </div>
    <? if (isset($data['errors']['form'])): ?>
        <div class="alert alert-danger" role="alert">
            <strong>
                <?= $data['errors']['form'] ?>
            </strong>
        </div>
    <? endif; ?>
    <button type="submit" class="btn btn-primary">Вход</button>
</form>