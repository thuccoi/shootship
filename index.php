<?php
include 'war.php';
if (isset($_POST['ship1']) && isset($_POST['ship2']) && isset($_POST['sizemap']) && isset($_POST['speed'])) {
    if ($_POST['ship1'] && $_POST['ship2']) {
        echo "<script type='text/javascript'>window.open('/ship/play.php?ship1=" . $_POST['ship1'] . "&ship2=" . $_POST['ship2'] . "&sizemap=" . $_POST['sizemap'] . "&speed=" . $_POST['speed'] . "', '_blank');</script>";
    }
}
?>
<form method="POST">

    Tàu 1
    <select name="ship1">
        <option value="thor"> Thor </option>
        <option value="hardy"> Hardy </option>
        <option value="son"> Sơn  </option>
        <option value="phuong"> Phương  </option>
        <option value="dat"> Đạt </option>
        <option value="kien"> Kiên </option>
    </select>
    <br>
    Tàu 2
    <select name="ship2" value="<?= isset($_POST['ship2']) ? $_POST['ship2'] : '' ?>">
        <option value="thor"> Thor </option>
        <option value="hardy"> Hardy </option>
        <option value="son"> Sơn  </option>
        <option value="phuong"> Phương  </option>
        <option value="dat"> Đạt </option>
        <option value="kien"> Kiên </option>
    </select>
    <br>
    Kích thước Map 
    <input name="sizemap" value="10" placeholder="sizemap" />
    <br>
    Tốc độ
    <input name="speed" value="10" placeholder="Tốc độ" />
    <br>
    <br>
    <input type="submit" value="Bắt đầu đấu">
</form>