import base64
import feedparser
from datetime import datetime
import requests
import json

def fetch_feed_data(feeds):
	allFeeds = []
	for feedObj in feeds:
		feed = feedparser.parse(feedObj["url"])
		site = feed.feed.title if 'title' in feed.feed else 'Unknown'
		logo = feed.feed.image.href if 'image' in feed.feed else 'No logo'
		for entry in feed.entries:
			image = entry.media_content[0]['url'] if 'media_content' in entry and entry.media_content else 'No image'
			entryJSON = {
				"title": entry.title,
				"link": entry.link,
				"published": entry.published,
				"summary": entry.summary,
				"site": site,
				"logo": logo,
				"image": image,
				"category_id": feedObj["category_id"]
			}
			allFeeds.append(entryJSON)
	return allFeeds

def save_feed_online(feed_data):
	url = "https://brique.in/savefeeds.php"
	headers = {
		'Content-Type': 'application/json',
		'Authorization': 'Bearer ' + base64.b64encode(b'briquetech').decode('utf-8')
	}
	payload = {"feeds": feed_data}
	response = requests.put(url, data=json.dumps(payload), headers=headers)
	return response.status_code, response.text

if __name__ == "__main__":
	feeds = [
		{"url": "https://venturebeat.com/category/ai/feed/", "category_id": 1},
		{"url": "https://techcrunch.com/category/artificial-intelligence/feed/", "category_id": 1},
		{"url": "https://www.wired.com/category/ai/feed/", "category_id": 1},
		{"url": "https://www.theverge.com/ai/rss/index.xml", "category_id": 1},
		{"url": "https://www.forbes.com/ai/feed/", "category_id": 1},
		{"url": "https://www.theverge.com/rss/ai-artificial-intelligence/index.xml", "category_id": 1},
	]

	feed_data = fetch_feed_data(feeds)
	status_code, response_text = save_feed_online(feed_data)
	print(f"Response Status Code: {status_code}")
	print(f"Response Text: {response_text}")