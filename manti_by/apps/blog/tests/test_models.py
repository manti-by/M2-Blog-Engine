import os
import uuid
import pytest
import shutil

from django.conf import settings
from unittest.mock import patch

from manti_by.apps.blog.models import Post


@pytest.mark.django_db
class PostModelTest:
    @classmethod
    def setup_class(cls):
        cls.data = {"name": str(uuid.uuid4())}

        release_path = os.path.join(settings.MEDIA_ROOT, "release")
        if not os.path.exists(release_path):
            os.makedirs(release_path)

        shutil.copy(
            os.path.join(settings.PROJECT_DIR, "static", "test", "test.mp3"),
            os.path.join(settings.MEDIA_ROOT, "release", "test.mp3"),
        )

    @classmethod
    def teardown_class(cls):
        release_path = os.path.join(settings.MEDIA_ROOT, "release")
        if os.path.exists(release_path):
            shutil.rmtree(release_path)

    def test_get(self):
        post = Post.objects.create(**self.data)

        assert post.name == self.data["name"]
        assert post.slug == self.data["name"].lower()

        assert post.created is not None
        assert post.translations_filled is False
        assert post.files_converted is False

    @patch("manti_by.apps.blog.models.generate_preview_for_post")
    def test_update(self, generate_mock):
        post = Post.objects.create(**self.data, is_music=True)

        assert post.is_music is True
        assert generate_mock.called is True

    def test_tags(self):
        post = Post.objects.create(**self.data)
        genre = str(uuid.uuid4())
        post.genre.add(genre)
        tag = str(uuid.uuid4())
        post.tags.add(tag)

        assert genre in post.genre.names()
        assert tag in post.tags.names()
        assert post.title == f"{self.data['name']} /{genre}/"

    @patch("manti_by.apps.blog.models.generate_preview_for_post")
    def test_files(self, generate_mock):
        post = Post.objects.create(**self.data, release="release/test.mp3")

        assert generate_mock.called is not None
        assert post.release_mp3_url is not None
        assert post.release_mp3_file is not None
        assert post.preview_mp3_url is not None
        assert post.preview_mp3_file is not None
        assert post.release_ogg_url is not None
        assert post.release_ogg_file is not None
        assert post.preview_ogg_url is not None
        assert post.preview_ogg_file is not None
