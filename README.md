Run: docker run -d --name invoice-backup -e API_URL=YOUR_API_URL -e API_KEY=YOUR_API_KEY --platform linux/arm64/v8 matsevh/invoice-backup:latest

Best Practice, make a volume to save the backups to container path: /app/backups
