import sqlite3
import os
import sys
import json
import urllib.parse
import re
from http.server import HTTPServer, SimpleHTTPRequestHandler

# Fix Windows console UTF-8 encoding
if sys.platform == 'win32':
    sys.stdout.reconfigure(encoding='utf-8')

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
os.chdir(BASE_DIR)

DB_FILE = os.path.join(BASE_DIR, 'military_db.sqlite')

MIME_TYPES = {
    '.css': 'text/css; charset=utf-8',
    '.js': 'application/javascript; charset=utf-8',
    '.json': 'application/json; charset=utf-8',
    '.png': 'image/png',
    '.jpg': 'image/jpeg',
    '.jpeg': 'image/jpeg',
    '.gif': 'image/gif',
    '.svg': 'image/svg+xml',
    '.ico': 'image/x-icon',
    '.woff': 'font/woff',
    '.woff2': 'font/woff2',
    '.ttf': 'font/ttf',
    '.html': 'text/html; charset=utf-8'
}

def get_db():
    conn = sqlite3.connect(DB_FILE)
    conn.row_factory = sqlite3.Row
    return conn

def init_db():
    conn = get_db()
    cursor = conn.cursor()
    cursor.execute('''
        CREATE TABLE IF NOT EXISTS military_personnel (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            manual_id VARCHAR(50),
            rank VARCHAR(100),
            surname VARCHAR(100),
            given_name VARCHAR(100),
            name_khmer VARCHAR(150),
            name_latin VARCHAR(150),
            gender VARCHAR(10) DEFAULT 'ប្រុស',
            id_card VARCHAR(50),
            position VARCHAR(150),
            unit_group VARCHAR(150),
            unit VARCHAR(150),
            rank_date DATE,
            position_date DATE,
            dob DATE,
            enlistment_date DATE,
            framework_date DATE,
            education_level VARCHAR(50),
            study_local INTEGER DEFAULT 0,
            study_abroad INTEGER DEFAULT 0,
            children_count INTEGER DEFAULT 0,
            black_card_expiry DATE,
            blue_card_expiry DATE,
            pob_village VARCHAR(100),
            pob_commune VARCHAR(100),
            pob_district VARCHAR(100),
            pob_province VARCHAR(100),
            place_of_birth TEXT,
            addr_house VARCHAR(100),
            addr_group VARCHAR(100),
            addr_village VARCHAR(100),
            addr_commune VARCHAR(100),
            addr_district VARCHAR(100),
            addr_province VARCHAR(100),
            current_address TEXT,
            marital_status VARCHAR(50),
            phone VARCHAR(50),
            notes TEXT,
            photo TEXT,
            family_photo TEXT,
            family_name VARCHAR(100),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ''')
    
    # Auto migrate table if columns are missing
    existing_cols = [col[1] for col in cursor.execute("PRAGMA table_info(military_personnel)").fetchall()]
    new_cols = {
        'manual_id': 'VARCHAR(50)',
        'surname': 'VARCHAR(100)',
        'given_name': 'VARCHAR(100)',
        'unit_group': 'VARCHAR(150)',
        'rank_date': 'DATE',
        'position_date': 'DATE',
        'education_level': 'VARCHAR(50)',
        'study_local': 'INTEGER DEFAULT 0',
        'study_abroad': 'INTEGER DEFAULT 0',
        'children_count': 'INTEGER DEFAULT 0',
        'black_card_expiry': 'DATE',
        'blue_card_expiry': 'DATE',
        'pob_village': 'VARCHAR(100)',
        'pob_commune': 'VARCHAR(100)',
        'pob_district': 'VARCHAR(100)',
        'pob_province': 'VARCHAR(100)',
        'addr_house': 'VARCHAR(100)',
        'addr_group': 'VARCHAR(100)',
        'addr_village': 'VARCHAR(100)',
        'addr_commune': 'VARCHAR(100)',
        'addr_district': 'VARCHAR(100)',
        'addr_province': 'VARCHAR(100)',
        'family_photo': 'TEXT',
        'family_name': 'VARCHAR(100)'
    }
    for col_name, col_type in new_cols.items():
        if col_name not in existing_cols:
            try:
                cursor.execute(f"ALTER TABLE military_personnel ADD COLUMN {col_name} {col_type}")
            except Exception as e:
                pass

    cursor.execute("SELECT COUNT(*) FROM military_personnel")
    if cursor.fetchone()[0] == 0:
        conn.close()
        try:
            import import_excel_data
            import_excel_data.import_excel()
        except Exception as e:
            print("Import excel failed:", e)
    else:
        conn.close()

class MilitaryRequestHandler(SimpleHTTPRequestHandler):
    def translate_path(self, path):
        parsed = urllib.parse.urlparse(path)
        clean_path = urllib.parse.unquote(parsed.path).lstrip('/')
        return os.path.join(BASE_DIR, clean_path)

    def do_GET(self):
        parsed = urllib.parse.urlparse(self.path)
        path = urllib.parse.unquote(parsed.path)

        if 'api.php' in path:
            self.handle_api_get(parsed)
            return

        if path in ('/', '/index.php'):
            filepath = os.path.join(BASE_DIR, 'index.php')
            if os.path.exists(filepath):
                with open(filepath, 'r', encoding='utf-8') as f:
                    content = f.read()
                content = re.sub(r'<\?php.*?\?>', '', content, flags=re.DOTALL)
                self.send_response(200)
                self.send_header('Content-Type', 'text/html; charset=utf-8')
                self.send_header('Access-Control-Allow-Origin', '*')
                self.end_headers()
                self.wfile.write(content.encode('utf-8'))
                return

        # Serve static assets (CSS, JS, images, fonts, etc.) directly with explicit MIME types
        clean_path = path.lstrip('/')
        filepath = os.path.join(BASE_DIR, clean_path)

        if os.path.isfile(filepath):
            ext = os.path.splitext(filepath)[1].lower()
            content_type = MIME_TYPES.get(ext, 'application/octet-stream')
            try:
                with open(filepath, 'rb') as f:
                    data = f.read()
                self.send_response(200)
                self.send_header('Content-Type', content_type)
                self.send_header('Content-Length', str(len(data)))
                self.send_header('Access-Control-Allow-Origin', '*')
                self.end_headers()
                self.wfile.write(data)
                return
            except Exception as e:
                pass

        super().do_GET()

    def do_POST(self):
        parsed = urllib.parse.urlparse(self.path)
        if 'api.php' in parsed.path:
            self.handle_api_post(parsed)
        else:
            super().do_POST()

    def handle_api_get(self, parsed):
        params = urllib.parse.parse_qs(parsed.query)
        action = params.get('action', [''])[0]

        conn = get_db()
        cursor = conn.cursor()

        if action == 'fetch_all':
            search_id = params.get('search_id', [''])[0].strip()
            search_name = params.get('search_name', [''])[0].strip()
            search = params.get('search', [''])[0].strip()
            rank = params.get('rank', [''])[0].strip()
            unit = params.get('unit', [''])[0].strip()
            status = params.get('marital_status', [''])[0].strip()

            query = "SELECT * FROM military_personnel WHERE 1=1"
            sql_params = []

            if search_id:
                query += " AND (id_card LIKE ? OR manual_id LIKE ?)"
                pattern_id = f"%{search_id}%"
                sql_params.extend([pattern_id, pattern_id])
            if search_name:
                query += " AND (name_khmer LIKE ? OR surname LIKE ? OR given_name LIKE ? OR name_latin LIKE ?)"
                pattern_name = f"%{search_name}%"
                sql_params.extend([pattern_name]*4)
            if search and not search_id and not search_name:
                query += " AND (name_khmer LIKE ? OR surname LIKE ? OR given_name LIKE ? OR name_latin LIKE ? OR id_card LIKE ? OR manual_id LIKE ? OR phone LIKE ? OR position LIKE ?)"
                pattern = f"%{search}%"
                sql_params.extend([pattern]*8)
            if rank:
                query += " AND rank = ?"
                sql_params.append(rank)
            if unit:
                query += " AND unit = ?"
                sql_params.append(unit)
            if status:
                query += " AND marital_status = ?"
                sql_params.append(status)

            query += " ORDER BY id ASC"

            cursor.execute(query, sql_params)
            rows = [dict(r) for r in cursor.fetchall()]
            conn.close()

            self.send_json({'success': True, 'count': len(rows), 'data': rows})

        elif action == 'get_stats':
            cursor.execute("SELECT COUNT(*) FROM military_personnel")
            total = cursor.fetchone()[0]
            conn.close()
            self.send_json({'success': True, 'data': {'total': total}})
        else:
            conn.close()
            self.send_json({'success': False, 'message': 'Unknown action'})

    def handle_api_post(self, parsed):
        params = urllib.parse.parse_qs(parsed.query)
        action = params.get('action', [''])[0]

        content_length = int(self.headers.get('Content-Length', 0))
        body = self.rfile.read(content_length).decode('utf-8')
        data = json.loads(body) if body else {}

        conn = get_db()
        cursor = conn.cursor()

        try:
            surname = data.get('surname', '').strip()
            given_name = data.get('given_name', '').strip()
            name_khmer = data.get('name_khmer', '').strip()
            if not name_khmer and (surname or given_name):
                name_khmer = f"{surname} {given_name}".strip()

            fields_tuple = (
                data.get('manual_id', ''),
                data.get('rank', ''),
                surname,
                given_name,
                name_khmer,
                data.get('name_latin', ''),
                data.get('gender', 'ប្រុស'),
                data.get('id_card', ''),
                data.get('position', ''),
                data.get('unit_group', ''),
                data.get('unit', ''),
                data.get('rank_date') or None,
                data.get('position_date') or None,
                data.get('dob') or None,
                data.get('enlistment_date') or None,
                data.get('framework_date') or None,
                data.get('education_level', ''),
                1 if data.get('study_local') else 0,
                1 if data.get('study_abroad') else 0,
                int(data.get('children_count') or 0),
                data.get('black_card_expiry') or None,
                data.get('blue_card_expiry') or None,
                data.get('pob_village', ''),
                data.get('pob_commune', ''),
                data.get('pob_district', ''),
                data.get('pob_province', ''),
                data.get('place_of_birth', ''),
                data.get('addr_house', ''),
                data.get('addr_group', ''),
                data.get('addr_village', ''),
                data.get('addr_commune', ''),
                data.get('addr_district', ''),
                data.get('addr_province', ''),
                data.get('current_address', ''),
                data.get('marital_status', 'នៅលីវ'),
                data.get('phone', ''),
                data.get('notes', ''),
                data.get('photo', ''),
                data.get('family_photo', ''),
                data.get('family_name', '')
            )

            if action == 'add':
                if not name_khmer:
                    raise Exception('សូមបញ្ចូល "ឈ្មោះខ្មែរ" ឬ "គោត្តនាម/នាម"!')

                cursor.execute('''
                    INSERT INTO military_personnel (
                        manual_id, rank, surname, given_name, name_khmer, name_latin, gender, id_card, position,
                        unit_group, unit, rank_date, position_date, dob, enlistment_date, framework_date,
                        education_level, study_local, study_abroad, children_count, black_card_expiry, blue_card_expiry,
                        pob_village, pob_commune, pob_district, pob_province, place_of_birth,
                        addr_house, addr_group, addr_village, addr_commune, addr_district, addr_province, current_address,
                        marital_status, phone, notes, photo, family_photo, family_name
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ''', fields_tuple)
                conn.commit()
                self.send_json({'success': True, 'message': 'បានរក្សាទុកទិន្នន័យបុគ្គលិកយោធាជោគជ័យ!'})

            elif action == 'edit':
                p_id = data.get('id')
                if not p_id:
                    raise Exception('សូមជ្រើសរើសបុគ្គលិកយោធាដែលត្រូវកែប្រែ!')

                cursor.execute('''
                    UPDATE military_personnel SET
                    id_card = ?, name_khmer = ?, name_latin = ?, gender = ?, dob = ?,
                    rank = ?, position = ?, unit = ?, place_of_birth = ?, current_address = ?,
                    enlistment_date = ?, framework_date = ?, marital_status = ?, phone = ?, notes = ?
                    WHERE id = ?
                ''', (
                    data.get('id_card', ''), data.get('name_khmer', ''), data.get('name_latin', ''),
                    data.get('gender', 'ប្រុស'), data.get('dob') or None, data.get('rank', ''),
                    data.get('position', ''), data.get('unit', ''), data.get('place_of_birth', ''),
                    data.get('current_address', ''), data.get('enlistment_date') or None,
                    data.get('framework_date') or None, data.get('marital_status', 'នៅលីវ'),
                    data.get('phone', ''), data.get('notes', ''), p_id
                ))
                conn.commit()
                self.send_json({'success': True, 'message': 'បានកែប្រែទិន្នន័យបុគ្គលិកយោធាជោគជ័យ!'})

            elif action == 'delete':
                p_id = data.get('id')
                cursor.execute("DELETE FROM military_personnel WHERE id = ?", (p_id,))
                conn.commit()
                self.send_json({'success': True, 'message': 'បានលុបទិន្នន័យបុគ្គលិកយោធាជោគជ័យ!'})

            elif action == 'import_batch':
                if not isinstance(data, list):
                    raise Exception('ទិន្នន័យនាំចូលមិនត្រឹមត្រូវ')

                success_count = 0
                for row in data:
                    name_khmer = (row.get('name_khmer') or row.get('ឈ្មោះខ្មែរ') or '').strip()
                    if not name_khmer:
                        continue
                    id_card = (row.get('id_card') or row.get('អត្តលេខ') or '').strip()

                    cursor.execute("SELECT id FROM military_personnel WHERE id_card = ?", (id_card,))
                    exists = cursor.fetchone()

                    fields = (
                        id_card, name_khmer,
                        (row.get('name_latin') or row.get('ឈ្មោះឡាតាំង') or '').strip(),
                        (row.get('gender') or row.get('ភេទ') or 'ប្រុស').strip(),
                        row.get('dob') or row.get('ថ្ងៃខែឆ្នាំកំណើត') or None,
                        (row.get('rank') or row.get('ឋានន្តរស័ក្តិ') or '').strip(),
                        (row.get('position') or row.get('មុខតំណែង') or '').strip(),
                        (row.get('unit') or row.get('អង្គភាព') or '').strip(),
                        (row.get('place_of_birth') or row.get('ទីកន្លែងកំណើត') or '').strip(),
                        (row.get('current_address') or row.get('ទីលំនៅបច្ចុប្បន្ន') or '').strip(),
                        row.get('enlistment_date') or row.get('ថ្ងៃខែឆ្នាំចូលបម្រើ') or row.get('ថ្ងៃខែឆ្នាំចូលបម្រើការងារ') or None,
                        row.get('framework_date') or row.get('ថ្ងៃចូលក្របខ័ណ្ឌ') or row.get('ថ្ងៃខែឆ្នាំចូលក្របខ័ណ្ឌ') or None,
                        (row.get('marital_status') or row.get('ស្ថានភាព') or row.get('ស្ថានភាពរស់នៅ') or 'នៅលីវ').strip(),
                        (row.get('phone') or row.get('លេខទូរស័ព្ទ') or '').strip(),
                        (row.get('notes') or row.get('ផ្សេងៗ') or '').strip()
                    )

                    if exists:
                        cursor.execute('''
                            UPDATE military_personnel SET
                            id_card=?, name_khmer=?, name_latin=?, gender=?, dob=?, rank=?, position=?,
                            unit=?, place_of_birth=?, current_address=?, enlistment_date=?, framework_date=?,
                            marital_status=?, phone=?, notes=? WHERE id_card=?
                        ''', fields + (id_card,))
                    else:
                        cursor.execute('''
                            INSERT INTO military_personnel (
                                id_card, name_khmer, name_latin, gender, dob, rank, position, unit,
                                place_of_birth, current_address, enlistment_date, framework_date,
                                marital_status, phone, notes
                            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                        ''', fields)
                    success_count += 1

                conn.commit()
                self.send_json({'success': True, 'message': f'នាំចូលទិន្នន័យពី Excel បានជោគជ័យសរុប {success_count} ជួរ!', 'imported': success_count})
        except Exception as e:
            conn.rollback()
            self.send_json({'success': False, 'message': str(e)})
        finally:
            conn.close()

    def send_json(self, data):
        self.send_response(200)
        self.send_header('Content-Type', 'application/json; charset=utf-8')
        self.send_header('Access-Control-Allow-Origin', '*')
        self.end_headers()
        self.wfile.write(json.dumps(data, ensure_ascii=False).encode('utf-8'))

if __name__ == '__main__':
    import socket
    init_db()
    port = int(os.environ.get('PORT', 8000))
    server_address = ('', port)
    httpd = HTTPServer(server_address, MilitaryRequestHandler)
    
    local_ip = '127.0.0.1'
    try:
        s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
        s.connect(('8.8.8.8', 80))
        local_ip = s.getsockname()[0]
        s.close()
    except Exception:
        pass

    print("============================================================")
    print(f" 🚀 កម្មវិធីគ្រប់គ្រងទិន្នន័យនាយទាហាន (Military System)")
    print(f" 💻 បើកលើកុំព្យូទ័រ (Computer): http://localhost:{port}")
    print(f" 📱 បើកលើទូរស័ព្ទ (Mobile Wi-Fi): http://{local_ip}:{port}")
    print("============================================================")
    httpd.serve_forever()
