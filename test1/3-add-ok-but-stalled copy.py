import libtorrent as lt
from qbittorrent import Client
import qbittorrentapi

import time

# Create a new torrent file
fs = lt.file_storage()
lt.add_files(fs, '/share/1.sql.zip')

# Create a torrent creator
creator = lt.create_torrent(fs)

with open('track.txt', 'r') as f: 
    lines = f.read().splitlines()
for line in lines:
    creator.add_tracker(line)

creator.set_creator('Lad01')

# Generate the torrent
lt.set_piece_hashes(creator, "/share/") #ith the path to your files
torrent = creator.generate()

# Write the torrent file
with open("/share/1.sql.zip.torrent", "wb") as f:  # Replace with the path where you want to save the torrent file
    f.write(lt.bencode(torrent))

print("Torrent file created successfully.")

conn_info = dict(
    host="12.0.0.54",
    port=8089,
    username="admin",
    password="4s!@#()()(",
)

# Add the torrent file to qBittorrent
# qb = Client('http://12.0.0.54:8089/')
# qb.login('admin', '4s!@#()()(')
# with open('/share/1.sql.zip.torrent', 'rb') as f:
# qb.torrents_add(torrent_files = '1.sql.zip.torrent')

with qbittorrentapi.Client(**conn_info) as qbt_client:
        if qbt_client.torrents_add(torrent_files ="/share/1.sql.zip.torrent") != "Ok.":
            raise Exception("Failed to add torrent.")

