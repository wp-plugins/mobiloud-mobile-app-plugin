#!/usr/bin/ruby
require 'fssm'

WP_PLUGIN_DIR = ".."

@haml_files = []
@sass_files = []
@coffee_files = []
@src_files = []

def load_maps
	@src_files = []
	File.open('src_map.csv','r').each_line do |line|
		params = line.strip.split(/\s+|\t+/)
		if params.count == 2
			@src_files << {src: params[0], dst: params[1]}
		end
	end

	#PHP HAML files
	@haml_files = []
	File.open('haml_map.csv','r').each_line do |line|
		params = line.strip.split(/\s+|\t+/)
		if params.count == 2
			@haml_files << {src: params[0], dst: params[1]}
		end
	end

	#SASS
	@sass_files = []
	File.open('sass_map.csv','r').each_line do |line|
		params = line.strip.split(/\s+|\t+/)
		if params.count == 2
			@sass_files << {src: params[0], dst: params[1]}
		end
	end

	#COFFEE
	@coffee_files = []
	File.open('coffee_map.csv','r').each_line do |line|
		params = line.strip.split(/\s+|\t+/)
		if params.count == 2
			@coffee_files << {src: params[0], dst: params[1]}
		end
	end

end

def print_maps
	puts "HAML"
	puts "--------"
	@haml_files.each do |h|
		puts "#{h}"
	end
	puts "--------"

	puts "SASS"
	puts "--------"
	@sass_files.each do |s|
		puts "#{s}"
	end
	puts "--------"

	puts "COFFEE"
	puts "--------"
	@coffee_files.each do |c|
		puts "#{c}"
	end
	puts "--------"


	puts "SRC"
	puts "--------"
	@src_files.each do |c|
		puts "#{c}"
	end
	puts "--------"

end


def compile_all
	load_maps
	puts "compiling all..."
	@haml_files.each do |hf|
		cmd = "php hamlphp.php #{hf[:src]} #{hf[:dst]}"
		system cmd
	end

	@sass_files.each do |sf|
		cmd = "sass #{sf[:src]} #{sf[:dst]}"
		system cmd
	end

	@coffee_files.each do |cf|
		cmd = "coffee -c  -o #{cf[:dst]} #{cf[:src]}"
		system cmd
	end

	@src_files.each do |sf|
		cmd = "cp #{sf[:src]} #{sf[:dst]}"
		system cmd
	end

end

def compile(file)
	load_maps
	hf = @haml_files.select{|hf| hf[:src] == file}.first
	if hf
		cmd = "php hamlphp.php #{hf[:src]} #{hf[:dst]}"
		puts cmd
		system cmd
		return
	end

	sf = @sass_files.select{|sf| sf[:src] == file}.first
	if sf
		cmd = "sass #{sf[:src]} #{sf[:dst]}"
		puts cmd
		system cmd
	end

	cf = @coffee_files.select{|cf| cf[:src] == file}.first
	if cf
		cmd = "coffee -c -o #{cf[:dst]} #{cf[:src]} "
		puts cmd
		system cmd
	end

	sf = @src_files.select{|sf| sf[:src] == file}.first
	if sf
		cmd = "cp #{sf[:src]} #{sf[:dst]} "
		puts cmd
		system cmd
	end
	

end

def monitor
	FSSM.monitor(".") do
	    update do |b, r|
	    	compile r
	    end
	 
	    create do |b, r|
	    	compile r
	    end
	 
	    delete do |b, r|
				hf = @haml_files.select{|hf| hf.src == file}.first
				sf = @sass_files.select{|sf| sf.src == file}.first
				cf = @coffee_files.select{|cf| cf.src == file}.first
				sf = @src_files.select{|sf| sf.src == file}.first

				if hf
					unlink hf[:src]
					unlink hf[:dst]				
				end

				if sf
					unlink sf[:src]
					unlink sf[:dst]				
				end

				if cf
					unlink cf[:src]
					unlink cf[:dst]				
				end
	    end
	end
end

compile_all
print_maps
if ARGV[0] == 'm'
	monitor
end