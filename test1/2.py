import libtorrent as lt
from qbittorrent import Client

# Create a torrent file
fs = lt.file_storage()
lt.add_files(fs, '/share/1.sql.zip')
create_torrent = lt.create_torrent(fs, piece_size=16384)  # Set piece size
torrent = lt.bencode(create_torrent.generate())
with open('1.sql.zip.torrent', 'wb') as f:
    f.write(torrent)

# Add the torrent file to qBittorrent
qb = Client('http://12.0.0.54:8089/')
qb.login('admin', '4s!@#()()(')
with open('1.sql.zip.torrent', 'rb') as f:
    qb.download_from_file(f)