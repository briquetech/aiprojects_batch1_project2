# This is AI Projects, Batch1, Project 2: Personal Newsletter

This project is a personal newsletter application with a chat interface similar to WhatsApp. The chat interface is hosted at [https://brique.in/chat.html](https://brique.in/chat.html). Open in responsive mode or on the mobile phone.

## Project Components

### getfeeds.php
This script is responsible for fetching the latest articles from various RSS feeds. It processes the feeds and returns the articles in a format suitable for the chat interface.

### messages.php
This script handles the messaging functionality within the chat interface. It manages sending and receiving messages, ensuring real-time communication between users.

### savefeeds.php
This script saves the fetched articles from the RSS feeds into the database. It ensures that the articles are stored correctly and can be retrieved when needed.

### synctoserver.py
This Python script is responsible for synchronizing the latest articles from the RSS feeds to the server. It periodically checks for new articles and sends them to the services, ensuring that the chat interface always has the most up-to-date content.

## How to Use
1. Access the chat interface at [https://brique.in/chat.html](https://brique.in/chat.html). Open in responsive mode or on the mobile phone.
2. Use the chat interface to communicate and receive the latest articles from your selected RSS feeds.

## License
This project is licensed under the MIT License.
