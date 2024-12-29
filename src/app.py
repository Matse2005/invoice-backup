from flask import Flask, send_from_directory, jsonify
import os
import subprocess

app = Flask(__name__)
BACKUP_DIR = './backups'


@app.route('/')
def index():
    return send_from_directory('.', 'index.html')


@app.route('/backups')
def list_backups():
    try:
        # Ensure the backup directory exists
        if not os.path.exists(BACKUP_DIR):
            return jsonify({'error': 'Backup directory does not exist'}), 404

        files = [f for f in os.listdir(BACKUP_DIR) if f.endswith('.json')]

        modified_files = []
        for f in files:
            parts = f.rsplit('-', 1)
            # Ensure the second part contains an extension
            if len(parts) > 1 and '.' in parts[1]:
                modified_files.append(parts[0] + ':' + parts[1])
            else:
                modified_files.append(f)

        return jsonify(modified_files)
    except Exception as e:
        return jsonify({'error': str(e)}), 500


@app.route('/backup/<filename>')
def get_backup(filename):
    return send_from_directory(BACKUP_DIR, filename)


@app.route('/force-backup', methods=['POST'])
def force_backup():
    try:
        print('Here 1')
        # Path to backup script
        backup_script = './backup.py'

        print('Here 2')
        # Run the backup script
        result = subprocess.run(
            ['python', backup_script], check=True, capture_output=True, text=True)

        print('Here 3')
        return jsonify({'message': 'Backup completed successfully!', 'output': result.stdout})
    except subprocess.CalledProcessError as e:
        return jsonify({'message': 'Backup failed', 'error': e.stderr}), 500
    except Exception as e:
        return jsonify({'message': f'Backup failed: {str(e)}'}), 500


if __name__ == '__main__':
    app.run(host='0.0.0.0')
