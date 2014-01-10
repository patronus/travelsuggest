<?php
	
	include 'qp.php';
	function getNearByResults($id,$title, $searchField, $store, $dist=5)
	{
		$url = prepareSolrQueryGeoDist($id, $title, $searchField, $store, $dist);
		$content = file_get_contents($url);
		return json_encode($content);	
	}

	function prepareSolrQueryGeoDist($id, $title, $searchFld, $store, $dist)
	{
		//$url="http://localhost:8983/solr/collection1/select?q=title:".urlencode($title);
		$url=SolrServer::getSearchUrl();
		$url=$url.'?q=title:'.urlencode($title);
		$i=0;
		$countSrchFld = count($searchFld);
		if($countSrchFld>0){
			$url = $url.urlencode(" AND ");
		
			for($i=0; $i<$countSrchFld-1; $i++)
			{
				$url = $url.urlencode("searchfield:".$searchFld[$i]." AND ");	
			}
			$url = $url.urlencode("searchfield:".$searchFld[$i]. " AND -level:1 AND -id:".$id);//Adding the last search field
		}
		$url = $url."&fq={!bbox}&pt=".$store."&sfield=store&d=".$dist."&fl=id,name,displaycontent,closedist:geodist()";//Adding the store and dist info
		$url = $url."&wt=json&indent=true&rows=5&sort=".urlencode("geodist() asc");
		//echo $url;
		return $url;
	}
?>
