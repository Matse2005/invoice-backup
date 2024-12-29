import os
import requests
import json
from datetime import datetime, timedelta

# Configuration
# API_URL = os.getenv('API_URL') + "/api/invoices"
# API_KEY = os.getenv('API_KEY')
API_URL = "https://invoice-server-main-yvc4uq.laravel.cloud/api/invoices"
API_KEY = "GKLdm2392J38iFgGPjcOOz2iQ327"
BACKUP_DIR = './backups'
FILE_AGE_LIMIT = 14  # days


def fetch_data():
    headers = {'Authorization': API_KEY}
    response = requests.get(API_URL, headers=headers)
    if response.status_code == 200:
        return response.json()
    else:
        raise Exception(f"Error: HTTP status code {response.status_code}")


def save_backup(data):
    os.makedirs(BACKUP_DIR, exist_ok=True)
    file_name = f"{BACKUP_DIR}/{datetime.now().strftime('%d-%m-%Y-%H-%M')}.json"
    with open(file_name, 'w') as file:
        json.dump(data, file)
    print(f"Data saved to {file_name}")


def clean_old_backups():
    limit = datetime.now() - timedelta(days=FILE_AGE_LIMIT)
    for file in os.listdir(BACKUP_DIR):
        file_path = os.path.join(BACKUP_DIR, file)
        if os.path.isfile(file_path) and datetime.fromtimestamp(os.path.getmtime(file_path)) < limit:
            os.remove(file_path)
            print(f"Deleted old file: {file}")


if __name__ == "__main__":
    try:
        data = fetch_data()
        print(data)
        save_backup(data)
        clean_old_backups()
    except Exception as e:
        print(e)
