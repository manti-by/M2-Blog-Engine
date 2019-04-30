# Generated by Django 1.10.7 on 2017-04-19 10:39

from django.db import migrations, models
import taggit.managers


class Migration(migrations.Migration):

    dependencies = [("blog", "0003_auto_20170419_0947")]

    operations = [
        migrations.RemoveField(model_name="post", name="related"),
        migrations.AddField(
            model_name="post",
            name="related",
            field=models.ManyToManyField(
                blank=True, null=True, related_name="_post_related_+", to="blog.Post"
            ),
        ),
        migrations.AlterField(
            model_name="post",
            name="tags",
            field=taggit.managers.TaggableManager(
                blank=True,
                help_text="A comma-separated list of tags.",
                through="taggit.TaggedItem",
                to="taggit.Tag",
                verbose_name="Tags",
            ),
        ),
    ]
