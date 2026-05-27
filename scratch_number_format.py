import os
import re

dir_path = 'resources/views/admin/laporan/exports'
for filename in os.listdir(dir_path):
    if filename.endswith('.blade.php'):
        filepath = os.path.join(dir_path, filename)
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Regex to match number_format(..., X, ',', '.')
        # We use a non-greedy match for the first argument: \s*(.+?)\s*
        # But since the first argument might contain parentheses (e.g. abs($val)), we should be careful.
        # Actually, since it's just blade, we can do a simpler replacement if we match everything up to the first comma that is followed by the decimals.
        # Let's match `number_format(` then anything until `, 0, ',', '.'` or `, 2, ',', '.'`
        # Because we only use 0 or 2 decimals in these exports.
        
        # Pattern for number_format(..., 0 or 2, ',', '.')
        # Group 1 is the value expression.
        pattern = r"number_format\(([^,]+?)(?:\s*,\s*(\d+)\s*,\s*\'[.,]\'\s*,\s*\'[.,]\')?\)"
        
        # But wait! Some expressions are `number_format($totalPenambahan + ($labaRugi >= 0 ? $labaRugi : 0), 0, ',', '.')`
        # `[^,]+?` will NOT match this because of the comma inside! wait, no comma in `$totalPenambahan + ($labaRugi >= 0 ? $labaRugi : 0)`.
        # So `[^,]+?` WILL work!
        
        def replace_format(match):
            val = match.group(1).strip()
            decimals = match.group(2) if match.group(2) else '0'
            # If the value is a number or variable, we can just output it.
            # We want: (request('export') == 'excel' ? (val) : number_format(val, decimals, ',', '.'))
            # But in Blade {{ ... }}, returning ternary is fine.
            return f"(request('export') == 'excel' ? ({val}) : number_format({val}, {decimals}, ',', '.'))"
        
        new_content = re.sub(pattern, replace_format, content)
        
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        
        print(f"Processed {filename}")
