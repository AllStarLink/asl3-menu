#!/usr/bin/python3

import argparse
import os
import re

# --------------------------------------------------

def move_block_to_end(content,
                      start_pattern,
                      next_block_start_pattern,
                      insert_before=None,
                      insert_after=None,
                      skip_blank_lines=False):
    in_block = False
    block = []
    new_content = []
    pre_comments = []

    insert_before = insert_before or []
    insert_after  = insert_after  or []

    for line in content:
        if not in_block:
            # look for our block beginning pattern
            if re.search(start_pattern, line):
                in_block = True
                block.extend(pre_comments)
                pre_comments = []
                block.append(line)

            # look for (and capture) a "special block comment" (3+ semicolons)
            elif re.match(r"^\s*;;;", line):
                pre_comments.append(line)

            # process a regular line
            else:
                new_content.extend(pre_comments)
                pre_comments = []
                new_content.append(line)

        else:
            # stop capturing when we reach the next block's start pattern
            if re.search(next_block_start_pattern, line):
                # check to see if we are also at the start of another block
                if re.search(start_pattern, line):
                    # ensure we have at least one blank line between blocks
                    if skip_blank_lines:
                        block.append("\n")
                    block.append(line)
                else:
                    in_block = False
                    new_content.append(line)

            elif skip_blank_lines and re.match(r"^\s*$", line):
                pass

            else:
                block.append(line)

    # add remaining special comments to new_content if not part of a block
    new_content.extend(pre_comments)
    pre_comments = []

    if block:
        return new_content, insert_before + block + insert_after
    else:
        return new_content, []

# --------------------------------------------------

def append_blocks(content, moved_blocks):
    # ensure "a" blank line before exists before the moved blocks
    if moved_blocks:
        # strip trailing newlines from the content
        while content and content[-1].strip() == "":
            content.pop()

        # check if the moved block already starts with a blank line
        if not (moved_blocks[0] and moved_blocks[0][0].strip() == ""):
            content.append("\n")

        # append the moved blocks at the end of the file
        for block in moved_blocks:
            content.extend(block)  # Append the block without extra blank lines

    return content

# --------------------------------------------------

def edit_lines(content,
               pattern,
               substitution,
               check_pattern=None):
    if check_pattern and any(re.search(check_pattern, line) for line in content):
        return content

    return [re.sub(pattern, substitution, line) for line in content]

# --------------------------------------------------

def remove_lines(content,
                 pattern,
                 check_pattern=None):
    if check_pattern and any(re.search(check_pattern, line) for line in content):
        return content

    return [line for line in content if not re.search(pattern, line)]

# --------------------------------------------------

def templatize(content, category):
    template_main = f'[{category}-main](!)'
    template      = f'[{category}]'
    template_ref  = f'[{category}]({category}-main)'

    _template_main = rf'^{re.escape(template_main)}'
    _template      = rf'^{re.escape(template)}(?!\()'
    _template_ref  = rf'^{re.escape(template_ref)}'

    if not any(re.search(_template_main, line) for line in content):
        # if no "[category-main](!)"
        if not any(re.search(_template, line) for line in content):
            # if no "[category]"
            return content

        # change "[category]" to "[category-main](!)"
        content = [re.sub(_template, template_main, line) for line in content]

    if any(re.search(_template_ref, line) for line in content):
        # if we already have "[category](category-main)"
        return content

    # add "[category](category-main)"
    new_content = []
    in_block = False
    for line in content:
        if not in_block:
            # look for our block beginning pattern
            if re.search(_template_main, line):
                in_block = True
                new_content.append(line)

            # process a regular line
            else:
                new_content.append(line)

        else:
            # stop capturing when we reach the next block's start pattern
            if re.search(r"^\[", line):
                in_block = False
                new_content.append(template_ref + "\n")
                new_content.append("\n")

            new_content.append(line)

    return new_content

# --------------------------------------------------

def update_rpt_conf(dir_in, dir_out):
    file_in  = os.path.join(dir_in,  "rpt.conf")
    file_out = os.path.join(dir_out, "rpt.conf")

    # read the current configuration file
    with open(file_in, 'r') as file:
        content = file.readlines()

    # make a few documentation updates
    content = edit_lines(content, r"Mores code", "Morse code")
    content = edit_lines(content, r"Not leagle", "Not legal")
    content = edit_lines(content, r"Morse Telemetry Frequency \*Changed$", "Morse Telemetry Frequency")

    # ensure that we have an [events] section
    content = edit_lines(content, r"^;\[events\]", "[events]", r"^\[events\]")

    # templatize categories
    content = templatize(content, r"events"       )
    content = templatize(content, r"functions"    )
    content = templatize(content, r"telemetry"    )
    content = templatize(content, r"morse"        )
    content = templatize(content, r"wait-times"   )

    # remove lines that, if present, we are going to put back
    content = remove_lines(content, r"^#tryinclude\s+\"?custom/rpt.conf\"?")
    content = remove_lines(content, r"^#tryinclude\s+\"?custom/rpt/\*.conf\"?")

    # remove a few lines that are out-of-place
    content = remove_lines(content, r"; Your node settings here ;")
    content = remove_lines(content, r"; Another node settings here ;")
    content = remove_lines(content, r"; Settings for another node ;")

    moved_blocks = []

    # move the "configure your nodes" block
    content, moved_block = move_block_to_end(content,
                                             r"; Configure your nodes here ;",
                                             r"^\[",
                                             ["#tryinclude \"custom/rpt.conf\"\n#tryinclude \"custom/rpt/*.conf\"\n\n"])
    if moved_block:
        moved_blocks.append(moved_block)

    # move the node [12345] blocks
    content, moved_block = move_block_to_end(content,
                                             r"^\[\d+]",
                                             r"^(\[|;\[1998])",
                                             [";;;;;;;;;;;;;;;;;;; Your node settings here ;;;;;;;;;;;;;;;;;;;\n"])
    if moved_block:
        moved_blocks.append(moved_block)

    # move the "settings for another node" block
    content, moved_block = move_block_to_end(content,
                                             r"^;\[1998]",
                                             r"^\[",
                                             [";;;;;;;;;;;;;;;;;; Settings for another node ;;;;;;;;;;;;;;;;;;\n"])
    if moved_block:
        moved_blocks.append(moved_block)

    content = append_blocks(content, moved_blocks)

    # write the updated configuration file
    with open(file_out, 'w') as file:
        file.writelines(content)

# --------------------------------------------------

def update_simpleusb_conf(dir_in, dir_out):
    file_in  = os.path.join(dir_in,  "simpleusb.conf")
    file_out = os.path.join(dir_out, "simpleusb.conf")

    # read the current configuration file
    with open(file_in, 'r') as file:
        content = file.readlines()

    # make a few documentation updates
    content = edit_lines(content, r"; ASL3 Tune settings ;", "; Tune settings ;")

    # remove lines that, if present, we are going to put back
    content = remove_lines(content, r"^#tryinclude\s+\"?custom/simpleusb.conf\"?")
    content = remove_lines(content, r"^#tryinclude\s+\"?custom/simpleusb/\*.conf\"?")

    moved_blocks = []

    # move the "configure your nodes" block
    content, moved_block = move_block_to_end(content,
                                             r"; Configure your nodes here ;",
                                             r"^\[",
                                             ["#tryinclude \"custom/simpleusb.conf\"\n#tryinclude \"custom/simpleusb/*.conf\"\n\n"])
    if moved_block:
        moved_blocks.append(moved_block)

    # move the node [12345] blocks
    content, moved_block = move_block_to_end(content,
                                             r"^\[\d+]",
                                             r"^\[")
    if moved_block:
        moved_blocks.append(moved_block)

    content = append_blocks(content, moved_blocks)

    # write the updated configuration file
    with open(file_out, 'w') as file:
        file.writelines(content)

# --------------------------------------------------

def update_usbradio_conf(dir_in, dir_out):
    file_in  = os.path.join(dir_in,  "usbradio.conf")
    file_out = os.path.join(dir_out, "usbradio.conf")

    # read the current configuration file
    with open(file_in, 'r') as file:
        content = file.readlines()

    # make a few documentation updates
    content = edit_lines(content, r"; ASL3 Tune settings ;", "; Tune settings ;")

    # remove lines that, if present, we are going to put back
    content = remove_lines(content, r"^#tryinclude\s+\"?custom/usbradio.conf\"?")
    content = remove_lines(content, r"^#tryinclude\s+\"?custom/usbradio/\*.conf\"?")

    moved_blocks = []

    # move the "configure your nodes" block
    content, moved_block = move_block_to_end(content,
                                             r"; Configure your nodes here ;",
                                             r"^\[",
                                             ["#tryinclude \"custom/usbradio.conf\"\n#tryinclude \"custom/usbradio/*.conf\"\n\n"])
    if moved_block:
        moved_blocks.append(moved_block)

    # move the node [12345] blocks
    content, moved_block = move_block_to_end(content,
                                             r"^\[\d+]",
                                             r"^\[")
    if moved_block:
        moved_blocks.append(moved_block)

    content = append_blocks(content, moved_blocks)

    # write the updated configuration file
    with open(file_out, 'w') as file:
        file.writelines(content)

# --------------------------------------------------

def parse_args():
    # Default values for the arguments
    default_dir_in  = "/etc/asterisk"
    default_dir_out = default_dir_in
    
    # Parse command line arguments
    parser = argparse.ArgumentParser(description="Specify the configuration directory and file.")
    parser.add_argument(
        "--dir-in", 
        type=str, 
        default=default_dir_in,
        help=f"Directory to \"read\" configuration files (default: \"{default_dir_in}\")"
    )
    parser.add_argument(
        "--dir-out", 
        type=str, 
        default=default_dir_out,
        help=f"Directory to \"write\" updated configuration files (default: \"{default_dir_out}\")"
    )
    
    args = parser.parse_args()
    
    # Ensure the directory exists
    if not os.path.isdir(args.dir_in):
        print(f"Error: The directory \"{args.dir_in}\" does not exist.")
        exit(1)
    if not os.path.isdir(args.dir_out):
        print(f"Error: The directory \"{args.dir_out}\" does not exist.")
        exit(1)
    if not os.access(args.dir_out, os.W_OK):
        print(f"Error: The directory \"{args.dir_out}\" is not writable.")
        exit(1)
    
    return args.dir_in, args.dir_out

def main():
    # parse arguments
    dir_in, dir_out = parse_args()

    # Update rpt.conf
    update_rpt_conf(dir_in, dir_out)

    # Update simpleusb.conf
    update_simpleusb_conf(dir_in, dir_out)
    
    # Update usbradio.conf
    update_usbradio_conf(dir_in, dir_out)

if __name__ == '__main__':
    main()

