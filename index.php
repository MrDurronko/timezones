<?php
date_default_timezone_set("UTC");
$vals = array('day', 'month', 'year', 'hour', 'minute', 'second');

$errors = array();
for ($i = 0; $i < count($vals); $i++) {
    if (!val($vals[$i])) {
        array_push($errors, strtoupper($vals[$i]) . ' has to be numeric and in sensible range');
    }
}
if (!empty($errors)) {
    form($errors);
}

$format = 'j-n-o G:i:s';
$date = $_GET['day'] . '.' . $_GET['month'] . '.' . $_GET['year'] . ' ' . $_GET['hour'] . ':' . $_GET['minute'] . ':' . $_GET['second'];
$signal = new DateTime($date);


$data = DateTimeZone::listIdentifiers();
$dates = array();

for ($i = 0; $i < count($data); $i++) {
    $offset = timezone_offset_get(timezone_open($data[$i]), $signal);
    $newDate = date($format, $signal->getTimestamp() + $offset);
    $dates[$data[$i]] = $data[$i] === 'UTC' || $offset !== 0 ? $newDate : 'no data';
}

echo '<h1>Ctrl + F your country/city</h1><br>';
//highlight_string(var_export($dates, true));


function val($input)
{
    $result = isset($_GET[$input]) && is_numeric($_GET[$input]);

    if (!$result) {
        return false;
    }

    switch ($input) {
        default:
            $result = false;
            break;

        case 'day':
            $result = $_GET['day'] <= 31 && $_GET[$input] > 0;
            break;

        case 'month':
            $result = $_GET['month'] <= 12 && $_GET[$input] > 0;
            break;

        case 'year':
            $result = $_GET[$input] > 0;
            break;

        case 'hour':
            $result = $_GET['hour'] < 24 && $_GET[$input] >= 0;
            break;

        case 'minute':
            $result = $_GET['minute'] < 60 && $_GET[$input] >= 0;
            break;

        case 'second':
            $result = $_GET['second'] < 60 && $_GET[$input] >= 0;
            break;
    }
    return $result;
}

function form($errors = array())
{
    $msg = '';
    if (!empty($errors)) {
        $msg = '<ul>';
        for ($i = 0; $i < count($errors); $i++) {
            $msg .= '<li>' . $errors[$i] . '</li>';
        }
        $msg .= '</ul>';
    }

    $form = '
<html>
<head></head>
<body>' . $msg . '
    <fieldset>
    <form action="" method="get">
        <label for="day">Day &nbsp; </label>
        <input id="day" name="day" type="text" value="';
    $form .= isset($_GET['day']) === true ? $_GET['day'] : null;
    $form .= '" required> (eg. 5)<br>

        <label for="month">Month &nbsp; </label>
        <input id="month" name="month" type="text" value="';
    $form .= isset($_GET['month']) === true ? $_GET['month'] : null;
    $form .= '" required> (eg. 7)<br>

        <label for="year">Year &nbsp; </label>
        <input id="year" name="year" type="text" value="';
    $form .= isset($_GET['year']) === true ? $_GET['year'] : null;
    $form .= '" required>(eg. 2018)<br>

        <label for="hour">Hour &nbsp; </label>
        <input id="hour" name="hour" type="text" value="';
    $form .= isset($_GET['hour']) === true ? $_GET['hour'] : null;
    $form .= '" required> (eg. 21)<br>

        <label for="minute">Minute &nbsp; </label>
        <input id="minute" name="minute" type="text" value="';
    $form .= isset($_GET['minute']) === true ? $_GET['minute'] : null;
    $form .= '" required> (eg. 05)<br>

        <label for="second">Second &nbsp; </label>
        <input id="second" name="second" type="text" value="';
    $form .= isset($_GET['second']) === true ? $_GET['second'] : null;
    $form .= '" required> (eg. 00)<br>

    <input type="submit" value="Calculate">
    </form>
</fieldset>
</body>
</html>
';
    die($form);
}

?>
<html>
<head>
    <style>
        tbody tr:nth-child(even) {
            background-color: #e4ebf2;
            color: #000;
        }
    </style>
</head>
<body>
<table>
    <?php foreach (array_keys($dates) as $city) { ?>
        <tr>
            <th><?php echo $city; ?></th>
            <td><?php echo $dates[$city]; ?></td>
        </tr>
    <?php } ?>
</table>
</body>
</html>