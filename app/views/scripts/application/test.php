<form class="form" method="get" action="/test-site">
    <div class="form-group">
        <input type="text" class="form-control"  placeholder="site" name="site" value="<?= $this->site?>">
    </div>
    <button type="submit" class="btn btn-default">Проверить</button>
</form>

<?php

if($this->parser) { ?>
<hr>
<table class="table table-bordered">
    <tr class="active">
        <td>Название проверки</td>
        <td>Статус</td>
        <td></td>
        <td>Текущее состояния</td>
    </tr>

    <tr>
        <td rowspan="2" >Проверка наличия файла robots.txt</td>

        <?php if($this->parser->getContent()) {?>
            <td class="success" rowspan="2">ок</td>
        <?php } else { ?>
            <td class="danger" rowspan="2">Ошибка</td>
        <?php } ?>

        <td>Состояние</td>
        <td><?= $this->parser->fileError()?></td>
    </tr>

    <tr>
        <td>Рекомендации</td>
        <td><?= $this->parser->fileRecommendation()?></td>
    </tr>

    <tr>
        <td rowspan="2" >Проверка указания директивы Host</td>
        <?php if($this->parser->getHostDirective()) {?>
        <td class="success" rowspan="2">ок</td>
        <?php } else { ?>
        <td class="danger" rowspan="2">Ошибка</td>
        <?php } ?>

        <td>Состояние</td>
        <td><?= $this->parser->hostError()?></td>
    </tr>

    <tr>
        <td>Рекомендации</td>
        <td><?= $this->parser->hostRecommendation()?></td>
    </tr>


    <?php if ($this->parser->getHostDirective()) {?>

        <tr>
            <td rowspan="2" >Проверка количества директив Host, прописанных в файле</td>
            <?php if($this->parser->getHostDirective() && count($this->parser->getHostDirective()) == 1) {?>
                <td class="success" rowspan="2">ок</td>
            <?php } else { ?>
                <td class="danger" rowspan="2">Ошибка</td>
            <?php } ?>

            <td>Состояние</td>
            <td><?= $this->parser->hostCountError()?></td>
        </tr>

        <tr>
            <td>Рекомендации</td>
            <td><?= $this->parser->hostCountRecommendation()?></td>
        </tr>

    <?php }?>


        <tr>
            <td rowspan="2" >Проверка размера файла robots.txt</td>
            <?php if($this->parser->checkFileSize()) {?>
                <td class="success" rowspan="2">ок</td>
            <?php } else { ?>
                <td class="danger" rowspan="2">Ошибка</td>
            <?php } ?>

            <td>Состояние</td>
            <td><?= $this->parser->sizeError()?></td>
        </tr>

        <tr>
            <td>Рекомендации</td>
            <td><?= $this->parser->sizeRecommendation()?></td>
        </tr>


    <tr>
        <td rowspan="2" >Проверка указания директивы Sitemap</td>
        <?php if($this->parser->getSitemaps()) {?>
            <td class="success" rowspan="2">ок</td>
        <?php } else { ?>
            <td class="danger" rowspan="2">Ошибка</td>
        <?php } ?>

        <td>Состояние</td>
        <td><?= $this->parser->siteMapError()?></td>
    </tr>

    <tr>
        <td>Рекомендации</td>
        <td><?= $this->parser->siteMapRecommendation()?></td>
    </tr>



    <tr>
        <td rowspan="2" >Проверка кода ответа сервера для файла robots.txt</td>
        <?php if($this->parser->getStatusCode() == '200') {?>
            <td class="success" rowspan="2">ок</td>
        <?php } else { ?>
            <td class="danger" rowspan="2">Ошибка</td>
        <?php } ?>

        <td>Состояние</td>
        <td><?= $this->parser->statusCodeError()?></td>
    </tr>

    <tr>
        <td>Рекомендации</td>
        <td><?= $this->parser->statusCodeRecommendation()?></td>
    </tr>

    <?php }?>
</table>


<a href="/excel" class="btn btn-primary">download</a>
