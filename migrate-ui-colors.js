import fs from 'fs';
import path from 'path';

const GRAY_SHADES = ['50', '100', '200', '300', '400', '500', '600', '700', '800', '900', '950'];

const REPLACEMENTS = {};

// Add gray -> surface replacements
for (const shade of GRAY_SHADES) {
    REPLACEMENTS[`text-gray-${shade}`] = `text-surface-${shade}`;
    REPLACEMENTS[`bg-gray-${shade}`] = `bg-surface-${shade}`;
    REPLACEMENTS[`border-gray-${shade}`] = `border-surface-${shade}`;
    REPLACEMENTS[`ring-gray-${shade}`] = `ring-surface-${shade}`;
    REPLACEMENTS[`hover:bg-gray-${shade}`] = `hover:bg-surface-${shade}`;
    REPLACEMENTS[`hover:text-gray-${shade}`] = `hover:text-surface-${shade}`;
    REPLACEMENTS[`hover:border-gray-${shade}`] = `hover:border-surface-${shade}`;
    REPLACEMENTS[`focus:bg-gray-${shade}`] = `focus:bg-surface-${shade}`;
    REPLACEMENTS[`focus:text-gray-${shade}`] = `focus:text-surface-${shade}`;
    REPLACEMENTS[`focus:border-gray-${shade}`] = `focus:border-surface-${shade}`;
    REPLACEMENTS[`divide-gray-${shade}`] = `divide-surface-${shade}`;
}

// Add blue -> primary replacements
for (const shade of GRAY_SHADES) {
    REPLACEMENTS[`text-blue-${shade}`] = `text-primary-${shade}`;
    REPLACEMENTS[`bg-blue-${shade}`] = `bg-primary-${shade}`;
    REPLACEMENTS[`border-blue-${shade}`] = `border-primary-${shade}`;
    REPLACEMENTS[`ring-blue-${shade}`] = `ring-primary-${shade}`;
    REPLACEMENTS[`hover:bg-blue-${shade}`] = `hover:bg-primary-${shade}`;
    REPLACEMENTS[`hover:text-blue-${shade}`] = `hover:text-primary-${shade}`;
    REPLACEMENTS[`hover:border-blue-${shade}`] = `hover:border-primary-${shade}`;
    REPLACEMENTS[`focus:bg-blue-${shade}`] = `focus:bg-primary-${shade}`;
    REPLACEMENTS[`focus:text-blue-${shade}`] = `focus:text-primary-${shade}`;
    REPLACEMENTS[`focus:border-blue-${shade}`] = `focus:border-primary-${shade}`;
    REPLACEMENTS[`divide-blue-${shade}`] = `divide-primary-${shade}`;
}

// Add indigo -> primary replacements
for (const shade of GRAY_SHADES) {
    REPLACEMENTS[`text-indigo-${shade}`] = `text-primary-${shade}`;
    REPLACEMENTS[`bg-indigo-${shade}`] = `bg-primary-${shade}`;
    REPLACEMENTS[`border-indigo-${shade}`] = `border-primary-${shade}`;
    REPLACEMENTS[`ring-indigo-${shade}`] = `ring-primary-${shade}`;
    REPLACEMENTS[`hover:bg-indigo-${shade}`] = `hover:bg-primary-${shade}`;
    REPLACEMENTS[`hover:text-indigo-${shade}`] = `hover:text-primary-${shade}`;
    REPLACEMENTS[`hover:border-indigo-${shade}`] = `hover:border-primary-${shade}`;
    REPLACEMENTS[`focus:bg-indigo-${shade}`] = `focus:bg-primary-${shade}`;
    REPLACEMENTS[`focus:text-indigo-${shade}`] = `focus:text-primary-${shade}`;
    REPLACEMENTS[`focus:border-indigo-${shade}`] = `focus:border-primary-${shade}`;
    REPLACEMENTS[`divide-indigo-${shade}`] = `divide-primary-${shade}`;
}


function walkDir(dir, callback) {
    fs.readdirSync(dir).forEach(f => {
        let dirPath = path.join(dir, f);
        let isDirectory = fs.statSync(dirPath).isDirectory();
        isDirectory ? walkDir(dirPath, callback) : callback(path.join(dir, f));
    });
}

let modifiedCount = 0;

function processDir(dirName) {
    walkDir(dirName, function (filePath) {
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
}

processDir('./resources/js/views');
processDir('./resources/js/components');

console.log(`Successfully migrated classes in ${modifiedCount} files.`);
