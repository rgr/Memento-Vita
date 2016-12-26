<?php // 18/03/2016

$data = importData("john.json");
$user = $data['user'];
$cat = $data['categories'];
$events = $data['events'];
$birth = secs2date($user['birth']['stamp']);
$death = secs2date($user['death']['stamp']);

function date2secs($date) {
	$secs = strtotime($date);

	if ($secs == FALSE) {
		$bug = new DateTime();
        $bug->setTimestamp(2147472000);
		$d = new DateTime($date);
		$diff = $bug->diff($d);
		$diff_sec = $diff->format('%r').(
		                ($diff->s)+
		                (60*($diff->i))+
		                (60*60*($diff->h))+
		                (24*60*60*($diff->d))+
		                (30*24*60*60*($diff->m))+
		                (365*24*60*60*($diff->y))
		                );
		// Add 2038 Timestamp
		$secs = 2147472000 + $diff_sec;
	}

	return $secs;
}

function secs2date($stamp) {
    $date = new DateTime();
    if ($stamp>2147472000) {
        $date->setTimestamp(2147472000);
        $s=$stamp-2147472000;
        $date->add(new DateInterval('PT'.$s.'S'));
    } else {
        $date->setTimestamp($stamp);
    }
    return $date;
}

function dateInfos($stamp = null, $week = null, $date = null, $user) {

	if ($stamp != null) {

		$weekPast = round(($stamp - $user['birth']['stamp']) / 604800);
		$weekLeft = abs(round(($user['death']['stamp'] - $stamp) / 604800));
		$birth = new DateTime(date("Y-m-d",$user['birth']['stamp']));
		$date = secs2date($stamp);
		$age = $date->diff($birth);
		$percent = round(($user['weeks']['total'] - $weekLeft) / $user['weeks']['total'] * 100);
		if ($user['weeks']['now'] > $weekLeft) {
		  $color = "#A2A2A2";
		  $class = "future";
		} elseif ($user['weeks']['now'] < $weekLeft) {
		  $color = "#2F2F2F";
		  $class = "past";
		} else {
		  $color = "#FF5A5A";
		  $class = "present";
		}

		$infos = array(
		               "stamp" => $stamp,
		               "year" => $date->format('Y'), //To Do
		               "month" => $date->format('M'), // To Do
		               "age" => $age->y,
		               "life" => $percent,
		               "weekPast" => $weekPast,
		               "weekLeft" => $weekLeft,
		               "color" => $color,
		               "class" => $class
		               );

		return $infos;
	} elseif ($week != null) {
		return dateInfos($user['birth']['stamp'] + ($week * 604800), null, null, $user);
	} elseif ($date != null) {
		if ($date == "now") {
			$stamp = time();
		} else {
			$stamp = date2secs($date);
		}
		return dateInfos($stamp, null, null, $user);
	}
}

function importData($filename = "data.json") {
	// Import events
	$data = json_decode(file_get_contents($filename), true);

    // User
    $now = new DateTime();
    $user = array(
              "birth" => array(
                               "date" => $data['user']['birth'],
                               "stamp" => date2secs($data['user']['birth'])),
              "death" => array(
                               "date" => $data['user']['death'],
                               "stamp" => date2secs($data['user']['death'])),
              "weeks" => array(
                               'total' => round((date2secs($data['user']['death']) - date2secs($data['user']['birth'])) / 604800),
                               'now' => round((date2secs($data['user']['death']) - $now->getTimestamp()) / 604800))
              );

    // Events & categories
	$colors = $data['colors'];
	$cat = array();
	foreach ($data['events'] as $catK => $catV) {
		$cat[$catK]['color'] = $colors[$catK];
		$catWeeks =  array();
		foreach ($catV as $event) {
			$eventWeeks =  array();
			$minWeek = 1000000;
            $minStamp = 7254403200; //2200
            $minDate = null;
            $maxStamp = 0; //2200
            $maxDate = null;
			foreach ($event['period'] as $period) {
				$begin = dateInfos(null, null, $period['begin'], $user);
				$end = dateInfos(null, null, $period['end'], $user);
                if($begin['stamp'] < $minStamp){
                    $minStamp = $begin['stamp'];
                    $minDate = $period['begin'];
                }
                if($end['stamp'] > $maxStamp){
                    $maxStamp = $end['stamp'];
                    $maxDate = $period['end'];
                }
				for ($i=$begin['weekPast']; $i <= $end['weekPast']; $i++) {
					($i < $minWeek)? $minWeek = $i: null;
					$eventWeeks[] = $i;
					$catWeeks[] = $i;
				}
			}
			$eventWeeks = implode('-', $eventWeeks);
            ($minDate == $maxDate)? $dates = $minDate : $dates = $minDate.' &bull; '.$maxDate;
			$events[$catK][] = array(
			               'name' => $event['name'],
                           'dates' => $dates,
                           'period' => $eventWeeks,
			               'minWeek' => $minWeek);
		}
		$cat[$catK]['period'] = implode('-', array_unique($catWeeks));
	}

	// Sort events by date
	foreach ($events as $catK => $catV) {
		$sort = array();
		foreach ($catV as $key => $value) {
			$sort[$key] = $value['minWeek'];
		}
		array_multisort($sort, SORT_ASC, $catV);
		$events[$catK] = $catV;
	}

	return array(
                 'user' => $user,
	             'categories' => $cat,
	             'events' => $events);
}
?>
<!DOCTYPE html>
<html lang='en'>
<head>
  <meta charset='utf-8'>
  <title>Memento Vita</title>

  <link href='//fonts.googleapis.com/css?family=Raleway:400,300,600' rel='stylesheet' type='text/css'>
  <link rel='stylesheet' href='css/normalize.css'>
  <link rel='stylesheet' href='css/skeleton.css'>
  <link rel="shortcut icon" href="favicon.ico"/>

  <script src='//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js'></script>
  <script src='https://google-code-prettify.googlecode.com/svn/loader/run_prettify.js'></script>
  <link rel='stylesheet' href='css/github-prettify-theme.css'>
  <script src='js/site.js'></script>
</head>
<body>

	<br />
    <div class='row'>
        <div class='one columns'>&nbsp;</div>
        <div class='ten columns' style='text-align:center'><h1>Memento Vita</h1></div>
    </div>

	<div class='row'>
		<div class='one column'>&nbsp;</div>
		<div class='ten columns'><h5><?php echo $birth->format("d-m-Y"); ?></h5></div>
	</div>

	<div class='row'>
		<div class='one column'>&nbsp;</div>
		<div class='ten columns'>
			<?php
			for ($w=1; $w < $user['weeks']['total'] + 1; $w++) {
				$i = dateInfos(null, $w, null, $user);
				echo "<div class='week ".$i['class']."' id='$w' style='background-color:".$i['color'].";width:9px;height:9px;margin:1px;float:left' title='
".$i['month']." ".$i['year']."
Age : ".$i['age']."
Life : ".$i['life']."%
Weeks left : ".$i['weekLeft']."'></div>";
			} ?>
		<div style='width:10px;height:25px;margin:1px;float:left;display:inline'>&nbsp;</div>
	  </div>
	</div>

	<div class='row'>
		<div class='one column'>&nbsp;</div>
        <div class='ten columns' style='text-align:right'><h5><?php echo $death->format("d-m-Y"); ?></h5></div>
	</div>

	<?php if ($events != NULL) { ?>
		<div class='row'>
			<div class='one column'>&nbsp;</div>
			<div class='ten columns' style='text-align:right'>
				<?php
				foreach ($events as $catK => $catV) {
	  				$eventsNb = count($catV);
	  				$catWeeks = array();
	  				$n = 0;
					foreach ($catV as $event) {
						$n++;
						echo '<span id="'.$event['period'].'|'.$cat[$catK]['color'].'" class="events" title="'.$event['dates'].'" style="cursor:pointer">'.$event['name'].'</span>';
						echo ($n < $eventsNb)? ' &bull; ' : null;
					}
					echo ' <b id="'.$cat[$catK]['period'].'|'.$cat[$catK]['color'].'" class="events" style="cursor:pointer">'.$catK.'</b>';
					echo '<br />';
				} ?>
			</div>
		</div>
	<?php } ?>
</body>
</html>
