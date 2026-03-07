import fs from 'fs';
import path from 'path';

const REPLACEMENTS = {
    'rounded-3xl': 'rounded-xl',
    'rounded-2xl': 'rounded-lg',
    'rounded-xl': 'rounded-md',
    'bg-slate-900': 'bg-primary-600',
    'text-slate-900': 'text-surface-900',
    'text-slate-800': 'text-surface-800',
    'text-slate-700': 'text-surface-700',
    'text-slate-600': 'text-surface-600',
    'text-slate-500': 'text-surface-500',
    'text-slate-400': 'text-surface-400',
    'bg-slate-50': 'bg-surface-50',
    'bg-slate-100': 'bg-surface-100',
    'bg-slate-200': 'bg-surface-200',
    'border-slate-200': 'border-surface-200',
    'border-slate-100': 'border-surface-100',
    'bg-sky-500': 'bg-primary-600',
    'bg-sky-600': 'bg-primary-700',
    'text-sky-500': 'text-primary-600',
    'text-sky-600': 'text-primary-700',
    'border-sky-500': 'border-primary-500',
    'ring-sky-200': 'ring-primary-200',
    'ring-sky-500': 'ring-primary-500',
    'shadow-lg shadow-slate-100': 'shadow-sm border border-surface-200',
};

function walkDir(dir, callback) {
    fs.readdirSync(dir).forEach(f => {
        let dirPath = path.join(dir, f);
        let isDirectory = fs.statSync(dirPath).isDirectory();
        isDirectory ? walkDir(dirPath, callback) : callback(path.join(dir, f));
    });
}

let modifiedCount = 0;

walkDir('./resources/js/views', function (filePath) {
    if (filePath.endsWith('.vue')) {
        let content = fs.readFileSync(filePath, 'utf8');
        let newContent = content;
        for (const [key, value] of Object.entries(REPLACEMENTS)) {
            newContent = newContent.split(key).join(value);
        }
        if (content !== newContent) {
            fs.writeFileSync(filePath, newContent, 'utf8');
            modifiedCount++;
            console.log(`Updated ${filePath}`);
        }
    }
});

walkDir('./resources/js/components', function (filePath) {
    if (filePath.endsWith('.vue')) {
        let content = fs.readFileSync(filePath, 'utf8');
        let newContent = content;
        for (const [key, value] of Object.entries(REPLACEMENTS)) {
            newContent = newContent.split(key).join(value);
        }
        if (content !== newContent) {
            fs.writeFileSync(filePath, newContent, 'utf8');
            modifiedCount++;
            console.log(`Updated ${filePath}`);
        }
    }
});

console.log(`Successfully migrated classes in ${modifiedCount} files.`);
