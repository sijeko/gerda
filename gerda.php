<?php
/**
 *
 *
 */

// Читаем настройки
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'settings.php');

define('GERDA_VERSION', '1.0');


//// Поехали!

echo 'Генератор коммитов Sijeko Gerda', PHP_EOL;
echo 'v', GERDA_VERSION, '                           ©  DeadWoroz, AlexDeg, MaximAL, Sijeko  2016', PHP_EOL, PHP_EOL;

// Валидация карты коммитов
if (!validateCommitMap($commits)) {
	echo 'Карта коммитов не ок:', PHP_EOL, implode('|' . PHP_EOL, $commits), '|', PHP_EOL, PHP_EOL;
	echo 'Должно быть ровно 7 строк по 52 символа каждая: 7 дней в неделе, 52 недели в году.', PHP_EOL;
	exit(1);
}
echo 'Карта коммитов ок:', PHP_EOL, implode('|' . PHP_EOL, $commits), '|', PHP_EOL, PHP_EOL;


// Считаем дни
$today = new \DateTime();
echo '                    Сегодня:  ', $today->format('c'), '    (неделя №', $today->format('W'), ')', PHP_EOL;
$todayYearAgo = clone $today;
$todayYearAgo->sub(new \DateInterval('P1Y'));
echo '             Год назад было:  ', $todayYearAgo->format('c'),
	'    (неделя №', $todayYearAgo->format('W'), ')', PHP_EOL;
echo '               Это был день:  ', getWeekDay($todayYearAgo), PHP_EOL;
$thatSunday = clone $todayYearAgo;
$thatSunday->sub(new \DateInterval('P' . $todayYearAgo->format('w') . 'D'));
echo 'Воскресенье той недели было:  ', $thatSunday->format('c'), PHP_EOL;


// Подсчёт и генерация коммитов
echo 'Считаем коммиты…', PHP_EOL;
$commitsToMake = generateCommits($commits, $thatSunday);

$command = '# Генерируем коммиты, чтобы карта коммитов на Гитхабе выглядела так:' . PHP_EOL . PHP_EOL;
$command .= '# ' . implode('|' . PHP_EOL . '# ', $commits) . '|' . PHP_EOL . PHP_EOL;
$command .= 'rm -rf .git' . PHP_EOL;
$command .= 'git init' . PHP_EOL . PHP_EOL;
$command .= 'echo "# Gerda" > gerda.md' . PHP_EOL . PHP_EOL;
foreach ($commitsToMake as $day => $count) {
	//echo "\t", $day, ' нужно коммитов: ', $count, "\t\t",
	//	'git commit -m "Gerda" --date="', $day, 'T12:00:00+0300"', PHP_EOL;

	$command .= 'echo "\n## ' . $day . '" >> gerda.md' . PHP_EOL;
	for ($commit = 0; $commit < $count; $commit++) {
		$command .= "\t" . 'echo "* Gerda №'. ($commit + 1) . '" >> gerda.md' . PHP_EOL;
		$command .= "\t" . 'git add gerda.md' . PHP_EOL;
		$command .= "\t" . 'git commit -m "Gerda №'. ($commit + 1) . '" --date="' . $day . 'T12:' .
			($commit < 10 ? '0' : '') . $commit . ':00+0300"' . PHP_EOL;
		$command .= PHP_EOL;
	}
	$command .= PHP_EOL;
}

$command .= 'git remote add origin ' . $origin . PHP_EOL;
$command .= 'git push -u origin master -f' . PHP_EOL;

echo 'Пишем файл с командами: ', $commandFile, PHP_EOL;
file_put_contents($commandFile, $command);
chmod($commandFile, 0740);

echo 'Адрес репозитория: ', $origin, PHP_EOL;


exit(0);




//// Вспомогательные функции

/**
 * @param \DateTime $dateTime
 * @return string
 */
function getWeekDay($dateTime)
{
	static $weekDays = ['воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'];
	return $weekDays[intval($dateTime->format('w'))];
}



/**
 * Проверка карты коммитов.
 * @param string[] $map
 * @return bool
 */
function validateCommitMap($map)
{
	if (count($map) !== 7) {
		//throw new \RuntimeException('В массиве должно быть ровно 7 элементов по 52 символа каждый.');
		return false;
	}
	foreach ($map as $str) {
		if (strlen($str) !== 52) {
			//throw new \RuntimeException('В массиве должно быть ровно 7 элементов по 52 символа каждый.');
			return false;
		}
	}
	return true;
}



/**
 * Генерация набора коммитов.
 *
 * @param  string[]   $map          Карта коммитов в виде массива из 7 строк по 52 символа каждая
 * @param  \DateTime  $firstSunday  Дата последнего воскресенья год назад
 * @return array
 */
function generateCommits($map, $firstSunday)
{
	if (!validateCommitMap($map)) {
		throw new \RuntimeException('В массиве должно быть ровно 7 элементов по 52 символа каждый.');
	}

	$commits = [];
	$count = 7 * 52;
	$date = clone $firstSunday;

	// Идём по всем символам карты коммитов, вычисляя неделю и день недели
	for ($day = 0, $weekDay = 0; $day < $count; $day++) {
		$week = intval($day / 7);
		$char = substr($map[$weekDay], $week, 1);
		if ($char !== ' ') {
			$commits[$date->format('Y-m-d')] = $char === '#' ? 20 : 10;
		}
		// Переходим к следующему дню
		$date->add(new DateInterval('P1D'));
		$weekDay = ($weekDay + 1) % 7;
	}

	return $commits;
}
