import libtorrent as lt
from qbittorrent import Client
import qbittorrentapi

import time

fname = "16.zip"

# Create a new torrent file
fs = lt.file_storage()
lt.add_files(fs, '/share/' + fname)

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
with open("/share/" + fname +".torrent", "wb") as f:  # Replace with the path where you want to save the torrent file
    f.write(lt.bencode(torrent))

print("Torrent file created successfully.")

conn_info = dict(
    host="12.0.0.54",
    port=8089,
    username="admin",
    password="adminadmin",
)


with qbittorrentapi.Client(**conn_info) as qbt_client:
    response = qbt_client.torrents_add(torrent_files="/share/" + fname +".torrent", save_path="/share/")
    if response != "Ok.":
        raise Exception("Failed to add torrent.")
    
print("sleep 1")
# Pause the torrent
time.sleep(2)

with qbittorrentapi.Client(**conn_info) as qbt_client:    
    # Get the info of all torrents
    torrents_info = qbt_client.torrents_info()


    # print("Torrents Info: ", torrents_info)
    
    # # Find the torrent that was just added
    for torrent in torrents_info:

        print("torrent.name = ", torrent.name)

        if torrent.name == fname:
            print("sleep 2, to stop, start torrent, sau đó các client mới tải seed này được, chưa rõ sao phải làm thế này")
            # Pause the torrent
            time.sleep(2)

            # qbt_client.torrents_set_upload_limit(torrent.hash, 100)
            qbt_client.torrents_pause(torrent.hash)
            
            print("sleep 2, to stop, start torrent, sau đó các client mới tải seed này được, chưa rõ sao phải làm thế này")
            # Pause the torrent
            time.sleep(2)
            
            # Resume the torrent
            qbt_client.torrents_resume(torrent.hash)
            break

