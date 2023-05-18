<h3>Добавить задачу</h3>
<form action="<?= $data['action'] ?>" method="post">
    <input type="hidden" name="backurl" value="<?= $data['backurl'] ?>">
    <? if (isset($data['edit_mode'])): ?>
        <input type="hidden" name="id" value="<?= $data['values']['id'] ?>">
    <? endif; ?>
    <div class="mb-3">
        <label class="form-label">Имя</label>
        <? if (isset($data['errors']['username'])): ?>
            <input type="text" class="form-control is-invalid" name="username" value="<?= $data['values']['username'] ?>">
            <div class="invalid-feedback">
                <?= $data['errors']['username'] ?>
            </div>
        <? else: ?>
            <input <?= (isset($data['edit_mode']) ? "disabled" : "") ?> type="text" class="form-control" name="username"
                value="<?= $data['values']['username'] ?>">
        <? endif; ?>
    </div>
    <div class="mb-3">
        <label class="form-label">Email</label>
        <? if (isset($data['errors']['useremail'])): ?>
            <input type="text" class="form-control is-invalid" name="useremail" value="<?= $data['values']['useremail'] ?>">
            <div class="invalid-feedback">
                <?= $data['errors']['useremail'] ?>
            </div>
        <? else: ?>
            <input <?= (isset($data['edit_mode']) ? "disabled" : "") ?> type="text" class="form-control" name="useremail"
                value="<?= $data['values']['useremail'] ?>">
        <? endif; ?>
    </div>
    <div class="mb-3">
        <label class="form-label">Текст</label>
        <? if (isset($data['errors']['text'])): ?>
            <input type="text" class="form-control is-invalid" name="text" value="<?= $data['values']['text'] ?>">
            <div class="invalid-feedback">
                <?= $data['errors']['text'] ?>
            </div>
        <? else: ?>
            <input type="text" class="form-control" name="text" value="<?= $data['values']['text'] ?>">
        <? endif; ?>
    </div>
    <button type="submit" class="btn btn-primary">Отправить</button>
</form>