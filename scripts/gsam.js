#!/usr/bin/env node
import simpleGit from 'simple-git';

const git = simpleGit();

async function squashMerge(source = 'develop', target = 'main', createTag = true, push = false) {
    try {
        // Assure-toi d'être à jour
        await git.fetch();

        // Bascule sur la branche cible
        await git.checkout(target);

        // SHA court de la source
        const sourceCommit = (await git.revparse([`${source}`])).trim().substring(0, 7);

        // Merge squash
        await git.raw(['merge', '--squash', '--allow-unrelated-histories', source]);

        // Commit unique
        const commitMessage = `Squash merge de ${source} @ ${sourceCommit}`;
        await git.commit(commitMessage);

        console.log(`✅ ${commitMessage}`);

        // Tag optionnel
        let tagName = null;
        if (createTag) {
            tagName = `merge-${source}-${sourceCommit}`;
            await git.addTag(tagName);
            console.log(`🏷️  Tag créé : ${tagName}`);
        }

        // Push optionnel
        if (push) {
            console.log(`⬆️  Push vers origin/${target}...`);
            await git.push('origin', target);

            if (tagName) {
                await git.pushTags('origin');
                console.log(`⬆️  Tag poussé : ${tagName}`);
            }
        }
    } catch (err) {
        console.error('❌ Erreur git:', err.message);
    }
}

// Arguments CLI
const args = process.argv.slice(2);
const source = args[0] || 'develop';
const target = args[1] || 'main';
const push = args.includes('--push');

squashMerge(source, target, true, push);
