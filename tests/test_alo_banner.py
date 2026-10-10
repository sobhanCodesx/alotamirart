#!/usr/bin/env python3
import base64
from pathlib import Path
import sys
import unittest

sys.path.insert(0, str(Path(__file__).resolve().parents[1] / "scripts"))
from alo_banner import make_banner


class EditorialBannerTests(unittest.TestCase):
    def test_refrigerator_gasket_banner_is_below_thirty_kib(self):
        output, size = make_banner({"template": "refrigerator-gasket", "max_bytes": 30720})
        self.assertLessEqual(size, 30720)
        self.assertTrue(base64.b64decode(output).startswith(b"RIFF"))

    def test_gasket_troubleshooting_variant_is_distinct_and_small(self):
        original, original_size = make_banner({"template": "refrigerator-gasket", "max_bytes": 30720})
        alternative, size = make_banner({"template": "refrigerator-gasket", "variant": "not-sticking", "max_bytes": 30720})
        self.assertNotEqual(original, alternative, "SEO article banners should not be duplicates")
        self.assertGreater(size, 6000)
        self.assertLessEqual(size, 30720)

    def test_unsupported_banner_variant_is_rejected(self):
        with self.assertRaises(ValueError):
            make_banner({"template": "refrigerator-gasket", "variant": "random"})

if __name__ == "__main__":
    unittest.main()
