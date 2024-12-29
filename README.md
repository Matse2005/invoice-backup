Run: docker run -d --name facturen-backup -p 9000:80 -e API_URL=YOUR_API_URL_INCLUDE_THE_SLASH_API -e API_KEY=YOUR_API_KEY --platform linux/arm64/v8 matsevh/invoice-backup:latest

Best Practice, make a volume to save the backups to container path: /var/www/html/backups
