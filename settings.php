<?php
/**
 * Settings for the commit art in the GitHub profile page.
 *
 */

// Streak graph picture
$commits = [
	/* columns
	'      10>|      20>|      30>|      40>|      50>|  ' < exactly 52 characters */
	'   *####     #      ###   #####   #   #    *###*    ',  // Sun
	'   #                  #   #       #  #    #     #   ',  // Mon
	'   #       #####      #   #       # #     #     #   ',  // Tue
	'   *###*     #        #   ###     ##*     #     #   ',  // Wed
	'       #     #        #   #       #  #    #     #   ',  // Thu
	'       #     #    #   #   #       #   #   #     #   ',  // Fri
	'   ####*   #####   *##*   #####   #    #   *###*    ',  // Sat
];
//  *  for 10 commmits in that day (lighter color)
//  #  for 20 commmits in that day (darker color)


// Repository origin
$origin = 'https://github.com/maximal/gerda.git';


// Output shell file
$commandFile = 'repo' . DIRECTORY_SEPARATOR . 'make-commits.sh';

//
