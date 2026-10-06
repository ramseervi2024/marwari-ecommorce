import re
import glob

def add_viewbox(filepath):
    with open(filepath, 'r') as f:
        content = f.read()

    # Regex to find <svg ...> that don't have viewBox
    # It replaces <svg ... > with <svg ... viewBox="0 0 24 24" >
    def replacer(match):
        svg_tag = match.group(0)
        if 'viewBox' not in svg_tag and 'viewbox' not in svg_tag:
            return svg_tag.replace('<svg', '<svg viewBox="0 0 24 24"')
        return svg_tag
        
    new_content = re.sub(r'<svg[^>]+>', replacer, content)
    
    if new_content != content:
        with open(filepath, 'w') as f:
            f.write(new_content)
        print(f"Fixed {filepath}")

for f in glob.glob('/Users/rameshseervi/Desktop/wordpress-erp-plugin/wp-ecommerce-plugin/*.html'):
    add_viewbox(f)
for f in glob.glob('/Users/rameshseervi/Desktop/wordpress-erp-plugin/wp-ecommerce-plugin/*.js'):
    add_viewbox(f)

print("Done")
