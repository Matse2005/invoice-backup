from flask import Flask, send_from_directory, jsonify, request, abort, session, redirect, url_for
import os
import subprocess
from flask_session import Session

app = Flask(__name__)
# Set a secret key for session management
app.config['SECRET_KEY'] = os.getenv('SECRET_KEY', 'supersecretkey')
app.config['SESSION_TYPE'] = 'filesystem'  # Store sessions in the file system
Session(app)

BACKUP_DIR = './backups'
ACCESS_KEY = os.getenv('ACCESS_KEY', 'supersecretaccesskey')


def check_access_key():
    if 'access_key' not in session or session['access_key'] != ACCESS_KEY:
        abort(403, description="Forbidden: Incorrect Access Key")


@app.route('/')
def index():
    if 'access_key' in session and session['access_key'] == ACCESS_KEY:
        return send_from_directory('.', 'index.html')
    return send_from_directory('.', 'login.html')


@app.route('/login', methods=['POST'])
def login():
    access_key = request.form.get('access_key')

    if access_key == ACCESS_KEY:
        session['access_key'] = access_key
        return redirect(url_for('index'))
    else:
        return 'Forbidden: Incorrect Access Key', 403


@app.route('/backups')
def list_backups():
    check_access_key()
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
    check_access_key()
    return send_from_directory(BACKUP_DIR, filename)


@app.route('/force-backup', methods=['POST'])
def force_backup():
    check_access_key()
    try:
        # Path to backup script
        backup_script = './backup.py'

        # Run the backup script
        result = subprocess.run(
            ['python', backup_script], check=True, capture_output=True, text=True)

        return jsonify({'message': 'Backup completed successfully!', 'output': result.stdout})
    except subprocess.CalledProcessError as e:
        return jsonify({'message': 'Backup failed', 'error': e.stderr}), 500
    except Exception as e:
        return jsonify({'message': f'Backup failed: {str(e)}'}), 500


if __name__ == '__main__':
    app.run(host='0.0.0.0')
