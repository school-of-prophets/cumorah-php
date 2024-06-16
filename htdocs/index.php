<!DOCTYPE html>
<?php
    include_once('../settings.php');
    include_once($src . '/fetcher.php');
    $path = $_GET['path'] ?? "/";
    $blob = fetchPath($path);
?>
<html>
<head>
    <title>Scripture Reader</title>
    <link rel="stylesheet" href="theme.css">
    <link rel="stylesheet" media="print" href="print.css">
    <link rel="stylesheet" media="screen" href="screen.css">
    <link rel="stylesheet" href="shared.css">
</head>
<body>
<div id="content">
    <? switch($blob['type'] ?? 'none') {
        case "root": { ?>
            <div class="root-page">
                <? if (!empty($blob['name'])) { ?>
                    <h1 class="root-name"><?= $blob['name'] ?></h1>
                <? } ?>
                <? foreach ($blob['libraries'] as $lib_key => $lib) { ?>
                    <div class="library-list">
                        <h2 class="library-name"><?= $lib['name'] ?? "?" ?></h2>
                        <div class="volume-list">
                            <? foreach ($lib['volumes'] as $vol_key => $vol) { ?>
                                <a class="volume-link" href="?path=<?= $vol['path'] ?>">
                                    <h3 class="volume-name"><?= $vol['name'] ?></h3>
                                </a>
                            <? } ?>
                        </div>
                    </div>
                <? } ?>
            </div><?
            break;
        }
        case "volume": { ?>
            <div class="volume-page">
                <div class="breadcrumbs">
                    <a class="breadcrumb-link" href="?path=/">Entire Library</a>
                </div>
                <h2 class="volume-name"><?= $blob['name'] ?></h2>
                <? foreach ($blob['collections'] as $c_key => $c) { ?>
                    <div class="collection-of-books">
                        <? if ($c['name']) { ?>
                            <h3 class="collection-name"><?= $c['name'] ?? "Unnamed Volume" ?></h3>
                        <? } ?>
                        <? foreach ($c['books'] as $book_key => $book) { ?>
                            <a class="book-link" href="?path=<?= $book['path'] ?>"> 
                                <h4 class="book-name"><?= $book['name'] ?></h4>
                                <? if ($book['aka']) { ?>
                                    <h4 class="book-aka"><small><?= $book['aka'] ?></small></h4>
                                <? } ?>
                            </a>
                        <? } ?>
                    </div>
                <? } ?>
            </div><? 
            break;
        }
        case "book": { ?>
            <div class="book-page">
                <div class="breadcrumbs">
                    <a class="breadcrumb-link" href="?path=/">Entire Library</a>
                    <? if (!empty($blob['volume']) && !empty($blob['volume']['path'])) { ?>
                        <a class="breadcrumb-link" href="?path=<?= $blob['volume']['path'] ?>"><?= $blob['volume']['name'] ?? "Book" ?></a>
                    <? } ?>
                </div>
                <h2 class="book-name"><?= $blob['name'] ?></h2>
                <? if (!empty($blob['aka'])) { ?>
                    <h3 class="book-aka"><?= $blob['aka'] ?></h3>
                <? } ?>
                <? if (!empty($blob['sum'])) { ?>
                    <? foreach (explode("||", $blob['sum']) as $sum_key => $sum ) { ?>
                        <p class="book-sum"><?= $sum ?></p>
                    <? } ?>
                <? } ?>
                <? foreach($blob['chapters'] as $c_key => $c) { ?>
                    <a class="chapter-link" href="?path=<?= $c['path'] ?>">
                        <h3 class="chapter-name"><?= $c['name'] ?></h3>
                        <? if (!empty($c['aka'])) { ?>
                            <h3 class="chapter-aka"><small><?= $c['aka'] ?></small></h3>
                        <? } ?>                            
                    </a>
                <? } ?>
            </div><? 
            break;
        }
        case "chapter": { ?>
            <div class="chapter-page">
                <div class="breadcrumbs">
                    <a class="breadcrumb-link" href="?path=/">Entire Library</a>
                    <? if (!empty($blob['volume']) && !empty($blob['volume']['path'])) { ?>
                        <a class="breadcrumb-link" href="?path=<?= $blob['volume']['path'] ?>"><?= $blob['volume']['name'] ?? "Volume" ?></a>
                    <? } ?>
                    <? if (!empty($blob['book']) && !empty($blob['book']['path'])) { ?>
                        <a class="breadcrumb-link" href="?path=<?= $blob['book']['path'] ?>"><?= $blob['book']['name'] ?? "Book" ?></a>
                    <? } ?>
                    <? if (!empty($blob['related'])) { ?>
                        <? if (!empty($blob['related']['prev']) && !empty($blob['related']['prev']['path'])) { ?>
                            <a class="breadcrumb-link" href="?path=<?= $blob['related']['prev']['path'] ?>"><?= $blob['related']['prev']['name'] ?? "Previous Chapter" ?></a>
                        <? } ?>
                        <? if (!empty($blob['related']['next']) && !empty($blob['related']['next']['path'])) { ?>
                            <a class="breadcrumb-link" href="?path=<?= $blob['related']['next']['path'] ?>"><?= $blob['related']['next']['name'] ?? "Next Chapter" ?></a>
                        <? } ?>
                    <? } ?>
                </div>

                <h2 class="chapter-name"><?= $blob['name'] ?? "Chapter" ?></h2>
                <? if (!empty($blob['aka'])) { ?>
                    <h3 class="chapter-aka"><?= $blob['aka'] ?></h3>
                <? } ?>
                <? if (!empty($blob['sum'])) { ?>
                    <? foreach (explode("||", $blob['sum']) as $sum_key => $sum ) { ?>
                        <p class="chapter-sum"><?= $sum ?></p>
                    <? } ?>
                <? } ?>
                <? foreach ($blob['content'] as $v_key => $v) { ?>
                    <? if (!empty($v['subchapter'])) { ?>
                        <h4 class="subchapter-name">
                            ${v.subchapter}
                        </h4>
                    <? } else if (!empty($v['svg'])) { ?>
                        <div class="inline-img">
                            <? foreach ($v['svg'] as $svg_key => $svg_line) { ?>
                                <?= $svg_line ?>
                            <? } ?>
                        </div>
                    <? } else if (empty($v['text']) && !empty($v['notes'])) { ?>
                        <? // this is the case for notes after an svg ?>
                        <div class="verse long-label">
                            <h4 class="verse-label">Notes:</h4>
                            <? foreach ($v['notes'] as $k => $note) { ?>
                                <p class="verse-text"> 
                                    <span class="note-label"><?= $k ?>.</span>
                                    <?= str_replace('||', '<br/>', $note) ?>
                                </p>
                            <? } ?>
                        </div>
                    <? } else {
                        $line_text = $v['text'] ?? "&nbsp;";
                        $line_text = preg_replace('/\{(.+?)\}/', '<sup title="{$1}">[$1]</sup>', $line_text);
                        $line_text = preg_replace('/\/\/(.+?)\/\//', '<i>$1</i>', $line_text);
                        $line_text = preg_replace('/\*\*(.+?)\*\*/', '<b>$1</b>', $line_text);
                        /* $foot_labels = */ preg_match_all('/\{([a-z0-9]+)\}/', $line_text, $foot_labels);
                        $foot_labels = array_merge(...$foot_labels);
                        $foot_labels = array_unique($foot_labels);
                        foreach($foot_labels as $k => $x) {
                            $note_x = $v['notes'][$x] ?? "No Note!";
                            $note_x = str_replace("\"", "&quot;", $note_x);
                            $line_text = preg_replace('/\{' . $x . '\}/', $note_x, $line_text);
                        } ?>
                        <div class="verse<?= strlen($v['v'] ?? "") > 3 ? " long-label" : "" ?><?= empty($v['v']) ? " no-label" : ""?>">
                            <? if (!empty($v['v'])) { ?>
                                <h4 class="verse-label"><?= $v['v'] ?></h4>
                            <? } ?>
                            <p class="verse-text"> 
                                <?= str_replace("||", "<br/>", $line_text) ?>
                            </p>
                        </div>
                    <? } ?>
                <? } ?>

                <div class="breadcrumbs">
                    <a class="breadcrumb-link" href="?path=/">Entire Library</a>
                    <? if (!empty($blob['volume']) && !empty($blob['volume']['path'])) { ?>
                        <a class="breadcrumb-link" href="?path=<?= $blob['volume']['path'] ?>"><?= $blob['volume']['name'] ?? "Volume" ?></a>
                    <? } ?>
                    <? if (!empty($blob['book']) && !empty($blob['book']['path'])) { ?>
                        <a class="breadcrumb-link" href="?path=<?= $blob['book']['path'] ?>"><?= $blob['book']['name'] ?? "Book" ?></a>
                    <? } ?>
                    <? if (!empty($blob['related'])) { ?>
                        <? if (!empty($blob['related']['prev']) && !empty($blob['related']['prev']['path'])) { ?>
                            <a class="breadcrumb-link" href="?path=<?= $blob['related']['prev']['path'] ?>"><?= $blob['related']['prev']['name'] ?? "Previous Chapter" ?></a>
                        <? } ?>
                        <? if (!empty($blob['related']['next']) && !empty($blob['related']['next']['path'])) { ?>
                            <a class="breadcrumb-link" href="?path=<?= $blob['related']['next']['path'] ?>"><?= $blob['related']['next']['name'] ?? "Next Chapter" ?></a>
                        <? } ?>
                    <? } ?>
                </div>
            </div><? 
            break;
        }
        case "none": { ?>
            <div>
                No content loaded for '<?= $path ?>'.
            </div><? 
            break;
        }
        default: { ?>
            <div>
                <?= $blob['name'] ?? "Unnamed Element" ?>
                <?= var_dump($blob) ?>
            </div><?
            break; 
        } ?>
    <? } ?>
</div>
</body>
</html>