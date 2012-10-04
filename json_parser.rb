require 'rubygems'
require 'json'
require 'net/http'

url = "http://demo.mobiloud.com/wp-content/plugins/mobiloud-mobile-app-plugin/posts.php"
resp = Net::HTTP.get_response(URI.parse(url))
data = resp.body

result = JSON.parse(data)

posts = result['posts']
post = posts.first
File.open("post_content.html", "w").write(post['content'])
