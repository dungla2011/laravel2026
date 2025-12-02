from qbittorrent import Client

qb = Client('http://12.0.0.54:8089/')

# Be sure to replace 'admin' and 'adminadmin' with your username and password
qb.login('admin', '4s!@#()()(')

# Print torrent list
torrents = qb.torrents()
for torrent in torrents:
    print(f"Name: {torrent['name']}, Seeds: {torrent['num_seeds']}")