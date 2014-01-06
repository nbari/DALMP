import os
import sys

rst = [f for f in os.listdir('.') if f.endswith('rst')]

for f in rst:
    name = f.split('.')[0]
    print name, len(name), f
    with open(f, 'w') as rst_file:
        rst_file.write("%s\n" % name)
        for x in range(len(name)):
            rst_file.write('=')
        rst_file.write("\n\nTodo")
