<?php
/**
* @Author Jason Cowdy & Katie Zagorski
* @Date: 5/6/11
* @comments KMeans based on work by claudio Brandolino and Federica Recantini
*/

define("JITTER", 2);

/**
* Clusters points using the kmeans clustering algorithm
* @param array $data the data points to cluster
* @param int $k The number of clusters to use 
* @return array A mixed array contiaining an array of centroids, and k arrays containing clusters and the indeces of the points it contains
*/
function kmeans($data, $k) {
	if($k <= 0)
	{
		echo "<div class=\"error span-15\">ERROR: K must be a positive integer greater than 0</div>";
		exit(0);
	}
        $oldCentroids = randomCentroids($data, $k);
	while (true)
	{
		$clusters = assign_points($data, $oldCentroids, $k);
		$newCentroids = calcCenter($clusters, $data);
		if ($oldCentroids === $newCentroids)
		{
			return(array ($newCentroids, $clusters));
		}
		$oldCentroids = $newCentroids;
	}
}



function calcCenter($clusters, $data)
{
	foreach($clusters as $num_cluster => $cluster_elements)
	{
		foreach ($cluster_elements as $cluster_element)
		{
			$cluster_elements_coords[$num_cluster][] = $data[$cluster_element];
		}
	}
	foreach ($cluster_elements_coords as $cluster_element_coords)
	{
		$cluster_centers[] = recenter($cluster_element_coords);
	}
	return $cluster_centers;
}



/**
* Calculates the center coordinates of a set of points
* @param array $coords An array of x and y points
* @return array An array containing the x and y coordinates of the center point
*/
function recenter($coords)
{
	foreach ($coords as $k)
	{
		$x = $x + $k[0];
		$y = $y + $k[1];
	}
	$center[0] = round($x / count($coords),2);
	$center[1] = round($y / count($coords),2);
	return $center;
}



/**
* Calculates the distance between two points
* @param array $v1 An integer array with x and y coordinate values
* @param array $v2 An integer array with x and y coordinate values
* @return double The distance between the two points
*/
function dist($v1, $v2)
{
	$x = abs($v1[0] - $v2[0]);
	$y = abs($v1[1] - $v2[1]);
	return round(sqrt(($x * $x) + ($y * $y)),2);
}


/**
* Assigns points to one of the centroids 
* @param array $data the data points to cluster
* @param array $centroids The array of centroids
* @param int $k The number of clusters
*/
function assign_points($data, $centroids, $k)
{
	foreach ($data as $datum_index => $datum)
	{
		foreach ($centroids as $centroid)
		{
			$distances[$datum_index][] = dist($datum, $centroid);
		}
	}
	foreach ($distances as $distance_index => $distance)
	{
		$which_cluster = min_key($distance);
		$tentative_clusters[$which_cluster][] = $distance_index;
		$distances_from_clusters = array("$distance_index" => $distance);
	}
	//in case there's not enough clusters, take the farthest element from any of the cluster's centres
	//and make it a cluster.
	if (count($tentative_clusters) < $k)
	{
		$point_as_cluster = max_key($distances_from_clusters);
		foreach ($tentative_clusters as $tentative_index => $tentative_cluster) 
		{
			foreach ($tentative_cluster as $tentative_element)
			{
				if ($tentative_element == $point_as_cluster)
				{
					$clusters[$k+1][] = $tentative_element;
				}
				else $clusters[$tentative_index][] = $tentative_element;
			}			
		}
	}
	else
	{
		$clusters = $tentative_clusters;
	}
	return $clusters;
}



/**
* Creates random starting clusters between the max and min of the data values
* @param $data array An array containing the 
* @param $k int The number of clusters
*/ 
function randomCentroids($data, $k) {
	foreach ($data as $j)
	{
		$x[] = $j[0];
		$y[] = $j[1];
	}
    
	for($k; $k > 0; $k--)
	{
                $centroids[$k][0] = rand(min($x), max($x));
                $centroids[$k][1] = rand(min($y), max($y));
    }
        return $centroids;
}


/**
* Gets the index of the min value in the array
* @param $array array The array of values to get the max index from
* @return int Index of the min value
*/
function min_key($array) {
	foreach ($array as $k => $val) {
		if ($val == min($array)) return $k;
	}
}



/**
* Gets the index of the max value in the array
* @param $array array The array of values to get the max index from
* @return int Index of the max value
*/
function max_key($array){
	foreach ($array as $k => $val) {
		if ($val == max($array)) return $k;
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

/**
* Loads data from MySQL into an array
* @param $column1 string The name of the first MySQL field to use
* @param $column2 string The name of the second MySQL field to use
* @return array The data loaded into two dimensional array, col1 is the first value, followed by col2
*/
function loadData($column1, $column2)
{
	if(0 == strcmp($column1, $column2))
		$query = "SELECT $column1 from " . TABLE  ." LIMIT 100";
	else
	    $query = "SELECT $column1, $column2 from " . TABLE . " LIMIT 100";

    $result = mysql_query($query);
    while($row = mysql_fetch_assoc($result))
    {
            $data[$i] = array($row[$column1], $row[$column2]);
                $i++;
    }
	
    return $data;
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

?>