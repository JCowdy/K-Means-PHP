<?
/**
* Graphs the kmeans results using Google Charts API
* @param $data array The original data array obtaiend from from the load data function
* @param $results The results array from the kmeans function
* @param $jitter The percentage of jitter to add
* @return void Echos the HTML img tag for the Google Charts Image
*/
function graph($data, $results, $jitter)
{
   	if($jitter)
		$data = addjitter($data);

	$clusters = array();

    foreach($results[1] as $v)
    {

        $count = 0;		//Build the chd data to append
        foreach($v as $k)
        {
			$x_coords = $x_coords . "," . $data[$k][0];
            $y_coords = $y_coords . "," . $data[$k][1];
            $count++;
        }
        array_push($clusters, $count);
    }

    $x_coords = trim($x_coords, ",");
    $y_coords = trim($y_coords, ",");

     echo    "<img src=\"https://chart.googleapis.com/chart?cht=s&chd=t:" .
             $x_coords . "|" . $y_coords .
             printCHM($clusters) . printScale($data) .
             "&chxt=x,y&chs=620x460\"><br>";
	echo $xmin . " - " . $xmax;
}
/**
* Adds jitter for visualization
* @param array $data The array of data to add jitter to
* @return array An array of data with jitter added to its points
*/
function addJitter($data)
{
		$max = max_value($data);
		$i = 0;
	foreach($data as $k)
	{
		if(rand(0,1))
			$data[$i][0] = $k[0] + ($max[0] * (rand(0,15)/1000));
		else
			$data[$i][0] = $k[0] - ($max[0] * (rand(0,15)/1000));
		

		if(rand(0,1))
			$data[$i][1] = $k[1] + ($max[1] * (rand(0,15)/1000));
		else
			$data[$i][1] = $k[1] - ($max[1] * (rand(0,15)/1000));

		$i++;
	}			
		return $data;
}


/**
* Assigns a color and shape to each cluster
* @param array $clusters The clusters array obtained from the kmeans results array
* @retrurn string The chart marker string to be appended to the Google Charts URL
*/
function printCHM($clusters)
{
    $colors = array("FF0000", "00FF00", "0000FF", "FFFF00", "00FFFF", "FF00FF");
    $shapes = array("o", "s", "d", "c", "x");

    $count = -1; $str = "&chm=o,FFFFFF,0,0,0|";
    for($i = 0; $i < count($clusters); $i++)
    {
        $str .= "x" . "," . $colors[$i%5] . ",0,";
        $str .= $count + 1;
        $count = $count + $clusters[$i];
        $str .= ":" . $count . ",5|";
    }
    return trim($str, "|");
}

/**
* Prints out text results of the kmeans clustering
* @param array $data The initial data points from loadData
* @param array $resutls The results array from the kmeans function
*/
function printResults($data, $results)
{
	$instances = array( );
	$count = 0;

	foreach($results[0] as $centroid)
	{
		echo "<b>Cluster" . ++$count . "</b><br>\r\n";
		
		echo nbsp(6) . "Centroid: (" .$centroid[0] . ", " . $centroid[1] . ")<br>\r\n";
		
		echo nbsp(6) . "Contains Points: ";
		foreach($results[1][$count-1] as $item)
		{
			$string .= ", " . $item;
		}
		echo trim($string, ",") . "<br><br>\r\n";
		$string = "";
		array_push($instances, count($results[1][$count-1]));

	}

	echo "<b>Clustered Instances:</b><br>\r\n";
	for($i = 0; $i < count($instances); $i++)
	{
		echo nbsp(6) . ($i+1) . nbsp(3) . $instances[$i] . nbsp(3) . $instances[$i]/array_sum($instances) . "<br>\r\n";
	}

}

/**
* Gets the max values of a two dimentional array
*/
function max_value($array){
	$x; $y;
	foreach($array as $row)
	{
		if($row[0] > $x)
			$x = $row[0];
		if($row[1] > $y)
			$y = $row[1];
	}
	return array($x, $y);
}

?>