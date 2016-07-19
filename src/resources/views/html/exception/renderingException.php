<style>
    pre.code_pre {
        position: relative;
        padding: 1em 0 2em 4em;
    }
    span.code_pre_numbers {
        border-right: 1px solid #ccc;
        color: #cc5922;
        left: 11px;
        padding-right: 1em;
        position: absolute;
        top: 11px;
    }
</style>

<div class="row">
    <div class="col-xs-10 col-xs-offset-1">
        <h1>
            <img src="<?php $this->asset('images/icons/system_icons/smile.png');?>" height="40px" style="margin-top:-12px"/>
            Exception catched!
        </h1>
    </div>
    <div class="col-xs-10 col-xs-offset-1">
        <h4><?php echo $e->getMessage()?></h4>
        <?php if ($trace[0]['function'] == 'path') { ?>
            <?php $minDist = 100; $bestWord = ''; $auxT = $e->getTrace(); $functionArg = $auxT[0]['args'][0]; ?>
            <p class="help-block">Possible paths are:
                <?php foreach (array_keys($routing) as $r) { ?>
                    <code><?= $r ?></code>&nbsp;
                    <?php if ($minDist > ($auxDist = levenshtein($functionArg, $r))) {
                        $minDist = $auxDist;
                        $bestWord = $r;
                    } ?>
                <?php } ?>
            </p>
            <h5 class="help-block">Did you mean <code><?= $bestWord ?></code>?</h5>
        <?php } ?>
        <hr/>
    </div>
    <div class="col-xs-10 col-xs-offset-1">
        <h3>Stacktrace</h3>
        <?php foreach ($trace as $t) {
            $fileName = $t['file'];
            $line = (int)$t['line'] - 1;
            $function = $t['function'];
            $functionArgs = $t['args'];
            $content = explode("\n", htmlentities(file_get_contents($fileName)));
            $fromLine = max($line - 2, 0);
            $toLine = min($line + 2, count($content) - 1);
            $lineContent = [];
            $lineNums = [];
            for ($i = $fromLine; $i <= $toLine; ++$i) {
                $lineContent[] = ($i == $line ? '<b>' : '') . $content[$i] . ($i == $line ? '</b>' : '') . " ";
                $lineNums[] = $i;
            }
            ?>
            <h6>File <code><?= $fileName; ?></code></h6>
            <pre class="code_pre"><?php echo implode("\n", $lineContent)?><span class="code_pre_numbers"><?php echo implode("\n", $lineNums); ?></span></pre>
            <hr/>
        <?php } ?>
        <hr/>
    </div>
    <div class="col-xs-10 col-xs-offset-1">
        <h3>Original stacktrace</h3>
        <pre style="max-height:500px;overflow-y:auto"><?php var_export($e->getTrace());?></pre>
    </div>
</div>



