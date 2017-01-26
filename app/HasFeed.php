<?php
namespace App;

trait HasFeed{
    
    public function feed(){
        $info = $this->info_for_feed();
        $episodes = $info['episodes'];
        $url = $info['url'];

		$output  = "<?xml version='1.0' encoding='UTF-8'?>\n";
		$output .= "<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\" xmlns:cc=\"http://web.resource.org/cc/\" xmlns:itunes=\"http://www.itunes.com/dtds/podcast-1.0.dtd\" xmlns:media=\"http://search.yahoo.com/mrss/\" xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\">\n";
		$output .= "<channel>\n";
		$output .= "<atom:link href=\"".$url."\" rel=\"self\" type=\"application/rss+xml\"/>
				<title>Recommendations for ".$info['name']."</title>
				<pubDate>".gmdate("D, d M Y G:i:s")." +0000</pubDate>
				<lastBuildDate>".gmdate("D, d M Y G:i:s")." +0000</lastBuildDate>
				<generator>".env('APP_NAME')."</generator>
				<link>".env('APP_URL')."</link>
				<language>en</language>
				<copyright><![CDATA[]]></copyright>
				<docs>".env('APP_URL')."</docs>
				<managingEditor>".env('MAILGUN_FROM_EMAIL_ADDRESS')."</managingEditor>
				<description><![CDATA[Recommendations for ".$info['name']." from ".env('APP_NAME')."]]></description>
				<image>
						<url>".env('APP_URL')."/img/logo.png</url>
						<title>".env('APP_NAME')."</title>
						<link><![CDATA[".env('APP_URL')."]]></link>
				</image>
				<itunes:author>".$info['name']."</itunes:author>
				<itunes:keywords></itunes:keywords>
				<itunes:image href=\"".env('APP_URL')."/img/logo.png\" />
				<itunes:explicit></itunes:explicit>
				<itunes:owner>
						<itunes:name><![CDATA[".$info['name']."]]></itunes:name>
						<itunes:email></itunes:email>
				</itunes:owner>
				<itunes:summary><![CDATA[Create your own podcast playlists at ".env('APP_URL')."]]></itunes:summary>
				<itunes:subtitle></itunes:subtitle>";
		foreach($episodes as $e){
			$output .= "<item>\n";
			$output .= "<title>" . str_replace("&","&amp;",$e->name) . "</title>\n";
			$output .= "<pubDate>" . gmdate("D, d M Y G:i:s",strtotime($e->pubdate)) . " +0000</pubDate>\n";
			$output .= "<guid isPermaLink=\"false\"><![CDATA[" . $e->guid . "]]></guid>\n";
			$output .= "<link><![CDATA[".$e->link."]]></link>\n";
			$output .= "<itunes:image href='".$e->img_url."' />\n";
			$output .= "<description><![CDATA[<a href='".env('APP_URL')."/episodes/".$e->slug."'>View this episode on ".env('APP_NAME')."</a><br><br>".$e->description."]]></description>\n";
			$output .= "<enclosure length=\"".$e->filesize."\" type=\"audio/mpeg\" url=\"".$e->url."\" />\n";
			$output .= "<itunes:duration>".$e->duration."</itunes:duration>\n";
			$output .= "<itunes:explicit>".$e->explicit."</itunes:explicit>";
			$output .= "<itunes:subtitle><![CDATA[<a href='".env('APP_URL')."/episodes/".$e->slug."'>View this episode on ".env('APP_NAME')."</a><br><br>".$e->description."]]></itunes:subtitle>";
			$output .= "</item>\n";
		}

		$output .= "</channel>\n";
		$output .= "</rss>";
		return $output;
    }
}